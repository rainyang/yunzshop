<?php

//微赞科技 by QQ:800083075 http://www.012wz.com/
if (!defined('IN_IA')) {
	die('Access Denied');
}
global $_W, $_GPC;

$op = $operation = $_GPC['op'] ? $_GPC['op'] : 'display';
$groups = m('member')->getGroups();
$levels = m('member')->getLevels();
$uniacid = $_W['uniacid'];
if ($op == 'display') {
	$pindex = max(1, intval($_GPC['page']));
	$psize = 10;
	$type = intval($_GPC['type']);
	if ($type == 3) {
		ca('finance.rechargelove.view');
	}
	$condition = ' ';
	$params = array(':uniacid' => $_W['uniacid'], ':type' => $type);
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
	if (!empty($_GPC['logno'])) {
		$logno="and so.ordersn='{$_GPC['logno']}'";
	}
	//$list = pdo_fetchall("select log.id,m.avatar,m.nickname,log.mid,log.pay,log.createtime,l.levelname from " . tablename('ewei_shop_commission_love') . " log " . " left join " . tablename('ewei_shop_member') . " m on m.openid=log.openid" ." left join " . tablename('ewei_shop_member_level') . " l on m.level =l.id" . " where 1 {$condition} ORDER BY log.createtime DESC limit " . ($pindex - 1) * $psize . ',' . $psize, $params);
	//平台基金
	$list = pdo_fetchall("select log.*,m.id as mid, m.realname,m.avatar,m.weixin,m.nickname,m.mobile,g.groupname,l.levelname from " . tablename('ewei_shop_commission_love') . " log " . " left join " . tablename('ewei_shop_member') . " m on m.openid=log.openid" . " left join " . tablename('ewei_shop_member_group') . " g on m.groupid=g.id" . " left join " . tablename('ewei_shop_member_level') . " l on m.level =l.id" . " where 1 {$condition} ORDER BY log.createtime DESC limit " . ($pindex - 1) * $psize . ',' . $psize, $params);
    //订单基金
	$conditiond=" so.status>=0 and sg.love>0 $logno  order by so.createtime desc limit " . ($pindex - 1) * $psize . ',' . $psize;
	$select="so.ordersn,so.createtime,sg.love,og.total,sm.id,sm.realname,sm.mobile,sm.nickname,sm.avatar,mg.groupname,ml.levelname  ";
	$sqlal=tablename('ewei_shop_order').' so left join '.tablename('ewei_shop_order_goods').' og on so.id=og.orderid ';
    $sqlal.='left join '.tablename('ewei_shop_goods').' sg on og.goodsid=sg.id left join '.tablename('ewei_shop_member').' sm on so.openid=sm.openid ';
    $sqlal.='left join '.tablename('ewei_shop_member_group').' mg on sm.groupid=mg.id left join '.tablename('ewei_shop_member_level').' ml on sm.level=ml.id';
	$dfs = pdo_fetchall("select {$select} from {$sqlal} where {$conditiond} ", array());
    //统计
    $sta='select count(*) from '.tablename('ewei_shop_order').' so left join '.tablename('ewei_shop_order_goods').' og on so.id=og.orderid' ;
    $sta.='  left join '.tablename('ewei_shop_goods').' sg on og.goodsid=sg.id where so.status>=0 and sg.love>0';
    $gdta=pdo_fetchcolumn($sta);
	
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
	$total = pdo_fetchcolumn("select count(*) from " . tablename('ewei_shop_commission_love') . " log " . " left join " . tablename('ewei_shop_member') . " m on m.openid=log.openid" . " left join " . tablename('ewei_shop_member_group') . " g on m.groupid=g.id" . " left join " . tablename('ewei_shop_member_level') . " l on m.level =l.id" . " where 1 {$condition} ", $params);
	if($_GPC['gtype']==2)$dfg=$total;
	if($_GPC['gtype']==1){$dfg=$gdta;$total=$gdta;}
	if($_GPC['gtype']==0)$dfg=$total+$gdta;
	
	$pager = pagination($total, $pindex, $psize);
} 
load()->func('tpl');
include $this->template('web/finance/love_log2');