<?php
global $_W, $_GPC;

$status      = intval($_GPC['status']);
if (empty($status)) {
	$status = 1;
}
$operation = empty($_GPC['op']) ? 'display' : $_GPC['op'];
if ($operation == 'display') {
	$channellevels = pdo_fetchall('SELECT * FROM ' . tablename('sz_yi_channel_level') . ' WHERE uniacid = :uniacid ORDER BY level_num DESC', array(':uniacid' => $_W['uniacid']));//渠道商等级
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	$condition = ' AND a.uniacid=:uniacid AND a.status=:status';
	$params = array(':uniacid' => $_W['uniacid'], ':status' => $status);
	if (!empty($_GPC['applyno'])) {
		$_GPC['applyno'] = trim($_GPC['applyno']);
		$condition .= ' AND a.applyno LIKE :applyno';
		$params[':applyno'] = "%{$_GPC['applyno']}%";
	}
	if (!empty($_GPC['realname'])) {
		$_GPC['realname'] = trim($_GPC['realname']);
		$condition .= ' AND (m.realname LIKE :realname OR m.nickname LIKE :realname OR m.mobile LIKE :realname)';
		$params[':realname'] = "%{$_GPC['realname']}%";
	}
	if (empty($starttime) || empty($endtime)) {
		$starttime = strtotime('-1 month');
		$endtime = time();
	}
	$timetype = $_GPC['timetype'];
	if (!empty($_GPC['timetype'])) {
		$starttime = strtotime($_GPC['time']['start']);
		$endtime = strtotime($_GPC['time']['end']);
		if (!empty($timetype)) {
			$condition .= " AND a.{$timetype} >= :starttime AND a.{$timetype}  <= :endtime ";
			$params[':starttime'] = $starttime;
			$params[':endtime'] = $endtime;
		}
	}
	if (!empty($_GPC['channel_level'])) {
		$condition .= ' AND m.channel_level=' . intval($_GPC['channel_level']);
	}
	if ($status = 3) {
		$orderby = 'finish_time';
	} else {
		$orderby = 'apply_time';
	}
	$sql = 'SELECT a.*, m.nickname,m.avatar,m.realname,m.mobile,l.level_name,l.level_num FROM ' . tablename('sz_yi_channel_apply') . ' a ' . ' left join ' . tablename('sz_yi_member') . ' m on m.id = a.mid' . ' left join ' . tablename('sz_yi_channel_level') . ' l on l.id = m.channel_level' . " WHERE 1  {$condition} ORDER BY {$orderby} DESC";
	if (empty($_GPC['export'])) {
		$sql .= '  LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
	}
	$list = pdo_fetchall($sql, $params);
	foreach ($list as &$row) {
		$row['apply_time'] = ($status >= 1 || $status == -1) ? date('Y-m-d H:i', $row['apply_time']) : '--';
		$row['finish_time'] = $status >= 3 ? date('Y-m-d H:i', $row['finish_time']) : '--';
		$row['typestr'] = empty($row['type']) ? '余额' : '微信';
	}
	unset($row);
	if ($_GPC['export'] == '1') {
		ca('channel.withdraw.export' . $status);
		plog('channel.withdraw.export' . $status, '导出数据');
		$title = "";
		if ($status == 1) {
			$title = '待审核佣金';
		} else if ($status == 3) {
			$title = '已打款佣金';
		}

		m('excel')->export($list, array('title' => $title . '数据-' . date('Y-m-d-H-i', time()), 'columns' => array(array('title' => 'ID', 'field' => 'id', 'width' => 12), array('title' => '提现单号', 'field' => 'applyno', 'width' => 24), array('title' => '粉丝', 'field' => 'nickname', 'width' => 12), array('title' => '姓名', 'field' => 'realname', 'width' => 12), array('title' => '手机号码', 'field' => 'mobile', 'width' => 12), array('title' => '提现方式', 'field' => 'typestr', 'width' => 12),array('title' => '申请佣金', 'field' => 'apply_money', 'width' => 12), array('title' => '申请时间', 'field' => 'apply_time', 'width' => 24), array('title' => '打款时间', 'field' => 'finish_time', 'width' => 24))));
	}
	$total = pdo_fetchcolumn('SELECT count(a.id) FROM' . tablename('sz_yi_channel_apply') . ' a ' . ' left join ' . tablename('sz_yi_member') . ' m on m.uid = a.mid' . ' left join ' . tablename('sz_yi_channel_level') . ' l on l.id = m.channel_level' . " WHERE 1 {$condition}", $params);
	$pager = pagination($total, $pindex, $psize);
} else if ($operation == 'detail') {
	$id = intval($_GPC['id']);
	$apply = pdo_fetch('SELECT * FROM ' . tablename('sz_yi_channel_apply') . ' WHERE uniacid=:uniacid AND id=:id limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $id));
	if (empty($apply)) {
		message('提现申请不存在!', '', 'error');
	}
	$applyid = $apply['mid'];
	$member = pdo_fetch('SELECT * FROM ' . tablename('sz_yi_member') . ' WHERE id = :id' , array(':id' => $applyid));
	$orderids = $apply['apply_ordergoods_ids'];
	if (empty($orderids)) {
		message('无任何订单，无法查看!', '', 'error');
	}
	$ordergoods_list = pdo_fetchall('SELECT * FROM ' . tablename('sz_yi_order_goods') . ' where  id in ( ' . $orderids . ' ) GROUP BY orderid');
	foreach ($ordergoods_list as $og) {
		$list[] = pdo_fetch('SELECT * FROM ' . tablename('sz_yi_order') . ' where  id = :id',array(':id' => $og['orderid']));
	}

	$totalpay = 0;
	foreach ($list as &$row) {
		$row['goods'] = pdo_fetchall('SELECT * FROM ' .tablename('sz_yi_order_goods') . ' WHERE orderid = :id', array(':id' => $row['id']));
		$totalpay += $row['price'];
	}
	unset($row);
	$totalcount = $total = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('sz_yi_order') . ' o ' . ' left join ' . tablename('sz_yi_member') . ' m on o.openid = m.openid ' . ' left join ' . tablename('sz_yi_member_address') . ' a on a.id = o.addressid ' . ' WHERE o.id in ( ' . $orderids . ' );');
	if (checksubmit('submit_check') && $apply['status'] == 1) {

		ca('channel.apply.pay');
		$time = time();
		$pay = $apply['apply_money'];
		if ($apply['type'] == 1 || $apply['type'] == 2) {
			$pay *= 100;
		} 

		if ($apply['type'] == 2) {
			if ($pay <= 20000 && $pay >= 1) {
				$result = m('finance')->sendredpack($apply['openid'], $pay, 0, $desc = '渠道商佣金提现金额', $act_name = '渠道商佣金提现金额', $remark = '渠道商佣金提现金额以红包形式发送');
			} else {
				message('红包提现金额限制1-200元！', '', 'error');
			}
		} else {
			$result = m('finance')->pay($apply['openid'], $apply['type'], $pay, $apply['applyno']);
		}
		
		if (is_error($result)) {
			if (strexists($result['message'], '系统繁忙')) {
				$updateno['applyno'] = $apply['applyno'] = m('common')->createNO('channel_apply', 'applyno', 'CA');
				pdo_update('sz_yi_channel_apply', $updateno, array('id' => $apply['id']));
				$result = m('finance')->pay($apply['openid'], $apply['type'], $pay, $apply['applyno']);
				if (is_error($result)) {
					message($result['message'], '', 'error');
				}
			}
			message($result['message'], '', 'error');
		}
		foreach ($list as $row) {

			foreach ($row['goods'] as $g) {
				$update = array('channel_apply_status' => 2);
				if (!empty($update)) {
					pdo_update('sz_yi_order_goods', $update, array('id' => $g['id']));
				}
			}
		}
		pdo_update('sz_yi_channel_apply', array('status' => 3, 'finish_time' => $time), array('id' => $id, 'uniacid' => $_W['uniacid']));
		$this->model->sendMessage($member['openid'], array('commission' => $pay, 'type' => $apply['type'] == 0?'余额':'微信'), TM_CHANNEL_APPLY_FINISH);
		$log = array('uniacid' => $_W['uniacid'], 'applyid' => $apply['id'], 'mid' => $member['id'], 'channel' => $totalchannel, 'channel_pay' => $totalpay, 'createtime' => $time);
		plog('channel.apply.pay', "佣金打款 ID: {$id} 申请编号: {$apply['applyno']} 总佣金: {$totalchannel} 审核通过佣金: {$totalpay} ");
		message('佣金打款处理成功!', $this->createPluginWebUrl('channel/apply', array('status' => $apply['status'])), 'success');
	}
}

load()->func('tpl');
include $this->template('withdraw');
