<?php
global $_W, $_GPC;
$openid = m('user')->getOpenid();
$id = intval($_GPC['id']);

$store = pdo_fetch("SELECT * FROM ".tablename('sz_yi_store')." WHERE id=:id and uniacid=:uniacid", array(':id' => $id, ':uniacid' => $_W['uniacid']));
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
     $totalwithdraw = pdo_fetchall('SELECT money FROM ' . tablename('sz_yi_store_withdraw') . ' WHERE uniacid = ' . $_W['uniacid'] . ' AND store_id = ' . $id);
     foreach ($totalwithdraw as  $value) {
         $totalwithdraws += $value['money'];
     }
     //未提现金额
     $canwithdraw =  $totalwithdrawprice - $totalwithdraws;

     show_json(1, array('totalprice' => $totalprice, 'ordercount' => $ordercount, 'store' => $store, 'canwithdraw' => $canwithdraw));
 }

include $this->template('index');
