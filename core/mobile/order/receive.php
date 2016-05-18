<?php
/*
require '../../../../../framework/bootstrap.inc.php';
require '../../../../../addons/sz_yi/defines.php';
require '../../../../../addons/sz_yi/core/inc/functions.php';
require '../../../../../addons/sz_yi/core/inc/plugin/plugin_model.php';
 */
global $_W, $_GPC;
//ignore_user_abort();
//set_time_limit(0);
$sets = pdo_fetchall('select uniacid from ' . tablename('sz_yi_sysset'));
foreach ($sets as $set) {
	$_W['uniacid'] = $set['uniacid'];
	if (empty($_W['uniacid'])) {
		continue;
	}
	$trade = m('common')->getSysset('trade', $_W['uniacid']);
	$days = intval($trade['receive']);
	if ($days <= 0) {
		continue;
	}
	$daytimes = 86400 * $days;
	$p = p('commission');
	$pcoupon = p('coupon');
	$orders = pdo_fetchall('select id,couponid from ' . tablename('sz_yi_order') . " where uniacid={$_W['uniacid']} and status=2 and sendtime + {$daytimes} <=unix_timestamp() ", array(), 'id');
	if (!empty($orders)) {
		$orderkeys = array_keys($orders);
		$orderids = implode(',', $orderkeys);
		if (!empty($orderids)) {
			pdo_query('update ' . tablename('sz_yi_order') . ' set status=3,finishtime=' . time() . ' where id in (' . $orderids . ')');
			foreach ($orders as $orderid => $o) {
				m('notice')->sendOrderMessage($orderid);
				if ($pcoupon) {
					if (!empty($o['couponid'])) {
						$pcoupon->backConsumeCoupon($o['id']);
					}
				}
				if ($p) {
					$p->checkOrderFinish($orderid);
				}
			}
		}
	}
}

//自动分红
//自动分红
$pbonus = p('bonus');
if(!empty($pbonus)){
	foreach ($sets as $set) {
		$_W['uniacid'] = $set['uniacid'];
		if (empty($_W['uniacid'])) {
			continue;
		}
		$daytime = strtotime(date("Y-m-d 00:00:00"));
		$isbonus = false;
		$bonus_set = $pbonus->getSet();
		//是否為月分紅
		if($bonus_set['sendmonth'] == 1){
			$monthtime = strtotime(date("Y-m-1 00:00:00"));
			//按月初時間查詢，如查詢到則已發放
			$bonus_data = pdo_fetchcolumn("select id from " . tablename('sz_yi_bonus') . " where utime>".$monthtime." and uniacid=".$_W['uniacid']."  order by id desc");
			if(empty($bonus_data)){
				$isbonus = true;	
			}
		}else{
			//按每天0點查詢，如查詢到則已發放
			$bonus_data = pdo_fetch("select * from " . tablename('sz_yi_bonus') . " where utime>".$daytime." and uniacid=".$_W['uniacid']."  order by id desc");
			if(empty($bonus_data)){
				$isbonus = true;	
			}
		}
		if($isbonus){
			if(!empty($bonus_set['start'])){
				$pbonus->autosend();
			}
			if(!empty($bonus_set['area_start'])){
				$pbonus->autosendall();
			}
		}
	}
}
echo "ok...";
