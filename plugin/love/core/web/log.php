<?php
if (!defined('IN_IA')) {
	die('Access Denied');
}
global $_W, $_GPC;
$op = $operation = $_GPC['op'] ? $_GPC['op'] : 'display';
$groups = m('member')->getGroups();
$levels = m('member')->getLevels();
$uniacid = $_W['uniacid'];
ca('love.log.view');
if ($op == 'display') {
	$pindex = max(1, intval($_GPC['page']));
	$psize = 10;
	$condition = ' ';
	$params = array(':uniacid' => $uniacid);
	if (!empty($_GPC['realname'])) {
		$_GPC['realname'] = trim($_GPC['realname']);
		$condition .= ' and (m.realname like :realname or m.nickname like :realname or m.mobile like :realname)';
		$params[':realname'] = "%{$_GPC['realname']}%";
	}
	if (empty($starttime) || empty($endtime)) {
		$starttime = strtotime('-1 month');
		$endtime = time();
	}
	if (!empty($_GPC['time'])) {
		$starttime = strtotime($_GPC['time']['start']);
		$endtime = strtotime($_GPC['time']['end']);
		if ($_GPC['searchtime'] == '1') {
			$condition .= " AND log.createtime >= :starttime AND log.createtime <= :endtime ";
			$params[':starttime'] = $starttime;
			$params[':endtime'] = $endtime;
		}
	}
	if (!empty($_GPC['level'])) {
		$condition .= ' and m.level=' . intval($_GPC['level']);
	}
	//贡献id
	if (!empty($_GPC['id'])) {
		$logno="and log.id='{$_GPC['id']}'";
	}
	//贡献类别
	if (!empty($_GPC['type'])) {
		$logno="and log.type='{$_GPC['$type']}'";
	}
	//贡献类别
	if (!empty($_GPC['paymonth'])) {
		$logno="and log.paymonth='{$_GPC['$paymonth']}'";
	}
	//平台基金
	$list = pdo_fetchall("select log.*, m.realname,m.avatar,m.weixin,m.nickname,m.mobile,g.groupname,l.levelname from " . tablename('sz_yi_love_log') . " log " . " left join " . tablename('sz_yi_member') . " m on m.openid=log.openid" . " left join " . tablename('sz_yi_member_group') . " g on m.groupid=g.id" . " left join " . tablename('sz_yi_member_level') . " l on m.level =l.id" . " where log.uniacid=:uniacid and log.status=0 {$condition} ORDER BY log.createtime DESC limit " . ($pindex - 1) * $psize . ',' . $psize, $params);
	
	if ($_GPC['export'] == 1) {

			ca('finance.rechargelove.export');
			plog('finance.rechargelove.export', '导出充值记录');

		foreach ($list as &$row) {
			$row['createtime'] = date('Y-m-d H:i', $row['createtime']);
			$row['groupname'] = empty($row['groupname']) ? '无分组' : $row['groupname'];
			$row['levelname'] = empty($row['levelname']) ? '普通会员' : $row['levelname'];
			if ($row['status'] == 0) {
				if ($row['type'] == 0) {
					$row['status'] = "未充值";
				} else {
					$row['status'] = "申请中";
				}
			} else {
				if ($row['status'] == 1) {
					if ($row['type'] == 0) {
						$row['status'] = "充值成功";
					} else {
						$row['status'] = "完成";
					}
				} else {
					if ($row['status'] == -1) {
						if ($row['type'] == 0) {
							$row['status'] = "";
						} else {
							$row['status'] = "失败";
						}
					}
				}
			}
			if ($row['rechargetype'] == 'system') {
				$row['rechargetype'] = "后台";
			} else {
				if ($row['rechargetype'] == 'wechat') {
					$row['rechargetype'] = "微信";
				} else {
					if ($row['rechargetype'] == 'alipay') {
						$row['rechargetype'] = "支付宝";
					}
				}
			}
		}
		unset($row);
		$columns = array(array('title' => '昵称', 'field' => 'nickname', 'width' => 12), array('title' => '姓名', 'field' => 'realname', 'width' => 12), array('title' => '手机号', 'field' => 'mobile', 'width' => 12), array('title' => '会员等级', 'field' => 'levelname', 'width' => 12), array('title' => '会员分组', 'field' => 'groupname', 'width' => 12), array('title' => empty($type) ? "充值金额" : "提现金额", 'field' => 'money', 'width' => 12), array('title' => empty($type) ? "充值时间" : "提现申请时间", 'field' => 'createtime', 'width' => 12));
		if (empty($_GPC['type'])) {
			$columns[] = array('title' => "充值方式", 'field' => 'rechargetype', 'width' => 12);
		}
		m('excel')->export($list, array("title" => (empty($type) ? "会员充值数据-" : "会员提现记录") . date('Y-m-d-H-i', time()), "columns" => $columns));
	}
	$total = pdo_fetchcolumn("select count(*) from " . tablename('sz_yi_love_log') . " log " . " left join " . tablename('sz_yi_member') . " m on m.openid=log.openid" . " left join " . tablename('sz_yi_member_group') . " g on m.groupid=g.id" . " left join " . tablename('sz_yi_member_level') . " l on m.level =l.id" . " where log.uniacid=:uniacid and log.status=0 {$condition} ", $params);
	$total_money = pdo_fetchcolumn("select sum(money) from " . tablename('sz_yi_love_log') . " where uniacid=:uniacid", array(":uniacid" => $_W['uniacid']));
	$love_money = pdo_fetchcolumn("select sum(love_money) from " . tablename('sz_yi_article') . " where uniacid=:uniacid", array(":uniacid" => $_W['uniacid']));
	$pager = pagination($total, $pindex, $psize);
} 
load()->func('tpl');
include $this->template('log');