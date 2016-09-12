<?php
global $_W, $_GPC;
$openid = m('user')->getOpenid();
if ($_W['isajax']) {
	$id = $_GPC['id'];
	$store = pdo_fetch("SELECT * FROM ".tablename('sz_yi_store')." WHERE id=:id and uniacid=:uniacid", array(':id' => $id, ':uniacid' => $_W['uniacid']));
	$order = pdo_fetchall(" SELECT * FROM ".tablename('sz_yi_order')." WHERE storeid=:id and uniacid=:uniacid and status >= 0", array(':uniacid' => $_W['uniacid'], ':id' => $id));
	$ordercount = count($order);
	//累计支付金额
	$totalprice = 0;

	foreach ($order as $value) {
		$totalprice += $value['price'];
	}
	//可以提现的金额
	$order_complete = pdo_fetchall(" SELECT * FROM ".tablename('sz_yi_order')." WHERE storeid=:id and uniacid=:uniacid and status =3", array(':uniacid' => $_W['uniacid'], ':id' => $id));
	$totalcanwithdraw = 0;
	foreach ($order_complete as $val) {
		$totalcanwithdraw += $val['price'];
	}
	if (!empty($store['balance'])) {
		$totalwithdrawprice = $totalcanwithdraw - $totalcanwithdraw * ($store['balance']/100);
	} else {
		$totalwithdrawprice = $totalcanwithdraw;
	}
	//已经提现的金额
	$totalwithdraw = pdo_fetchall('SELECT money FROM ' . tablename('sz_yi_store_withdraw') . ' WHERE uniacid = :uniacid AND store_id = :id AND status = 1', array(':uniacid' => $_W['uniacid'], ':id' => $id));
	foreach ($totalwithdraw as  $value) {
		$totalwithdraws += $value['money'];
	}
	//未提现金额
	$canwithdraw =  $totalwithdrawprice - $totalwithdraws;
	//待打款金额
	$wait_apply = pdo_fetchall('SELECT money FROM ' . tablename('sz_yi_store_withdraw') . ' WHERE uniacid = :uniacid AND store_id = :id AND status = 0', array(':uniacid' => $_W['uniacid'], ':id' => $id));
    if (!empty($wait_apply)) {
        foreach ($wait_apply as  $value) {
            $wait_applyed += $value['money'];
        }
    } else {
        $wait_applyed = 0;
    }

	show_json(1, array('totalprice' => $totalprice, 'ordercount' => $ordercount, 'store' => $store, 'canwithdraw' => $canwithdraw, 'withdraw_money' => $totalwithdraws, 'canwithdrawtotal' => $totalwithdrawprice, 'wait_apply' => $wait_applyed, 'storeid' => $id));

}
include $this->template('my_pocket');
