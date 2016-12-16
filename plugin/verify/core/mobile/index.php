<?php
global $_W, $_GPC;
$openid = m('user')->getOpenid();
$id = intval($_GPC['id']);

$store = pdo_fetch("SELECT * FROM ".tablename('sz_yi_store')." WHERE id=:id and uniacid=:uniacid", array(':id' => $id, ':uniacid' => $_W['uniacid']));
$set = $this->getSet();
 if ($_W['isajax']) {
     $id = $_GPC['id'];
     $store = pdo_fetch("SELECT * FROM ".tablename('sz_yi_store')." WHERE id=:id and uniacid=:uniacid", array(':id' => $id, ':uniacid' => $_W['uniacid']));

     //累计成交金额
     $totalprice = $this->model->getTotalPrice($id);
     //订单数量
     $ordercount = $this->model->getTotal($id);
     //可以提现的金额
     $totalwithdrawprice = $this->model->getRealPrice($id);
     //已经提现的金额
     $totalwithdraws = $this->model->getWithdrawed($id);
     //未提现金额
     $canwithdraw =  $totalwithdrawprice - $totalwithdraws;

     return show_json(1, array('totalprice' => $totalprice, 'ordercount' => $ordercount, 'store' => $store, 'canwithdraw' => $canwithdraw));
 }

include $this->template('index');
