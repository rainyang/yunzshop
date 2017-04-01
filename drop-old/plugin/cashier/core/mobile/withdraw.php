<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$page = 'withdraw';
$openid = m('user')->getOpenid();
$member = m('member')->getInfo($openid);
$id=$_GPC['id']? $_GPC['id'] : '0';
$store = pdo_fetch('select * from ' . tablename('sz_yi_cashier_store') . ' where uniacid=:uniacid and id=:id', array(
    ':uniacid' => $_W['uniacid'], ':id' => $id
));

$cashier_order = pdo_fetchall('SELECT order_id FROM ' . tablename('sz_yi_cashier_order') . ' WHERE uniacid = :uniacid AND cashier_store_id = :cashier_store_id', array(':uniacid' => $_W['uniacid'], ':cashier_store_id' => $store['id']));
$orderidss = false;

foreach ($cashier_order as $order) {
    if($order['order_id']){
         $orderidss = true;
         $orderids .= "'".$order['order_id']."',";
    }
    
}

$totalprices = 0;
if ($orderidss) {
    // 累计支付金额
    $orderids = substr($orderids,0,-1);
    $totalprices = pdo_fetch('SELECT SUM(realprice) AS tprice FROM ' . tablename('sz_yi_order') . ' WHERE uniacid = ' . $_W['uniacid'] . ' AND id IN ( '.$orderids.' ) AND status = 3');
    $totalprices = $totalprices['tprice'];
    // 已经提现的金额
    $totalwithdraw = pdo_fetchall('SELECT money FROM ' . tablename('sz_yi_cashier_withdraw') . ' WHERE uniacid = ' . $_W['uniacid'] . ' AND cashier_store_id = ' . $id);
    foreach ($totalwithdraw as  $value) {
        $totalwithdraws += $value['money'];
    }
    
    $totalprices = $totalprices - $totalwithdraws;
    $totalpricess = number_format($totalprices,'2');
}

if ($operation == 'display' && $_W['isajax']) {
    $store['totalprices'] = $totalpricess;
    return show_json(1, array(
        'store'  => $store,
        'noinfo' => empty($member['realname'])
    ));
} else if ($operation == 'submit' && $_W['ispost']) {
    $money = floatval($_GPC['money']);
    
    if (empty($money)) {
        return show_json(0, '申请金额为空!');
    }
    if ($money <= 0) {
        return show_json(0, '提现金额不能小于0元!');
    }
    if ($money > $totalprices) {
        return show_json(0, '提现金额过大!');
    }
   
    $withdraw_no = m('common')->createNO('cashier_withdraw', 'withdraw_no', 'CW');
    $data = array(
        'uniacid'           => $_W['uniacid'],
        'withdraw_no'       => $withdraw_no,
        'openid'            => $openid,
        'cashier_store_id'  => $id,
        'money'             => $money,
        'status'            => 0
    );
    pdo_insert('sz_yi_cashier_withdraw', $data);
    $msg = array(
                            'keyword1' => array('value' => '收银台提现成功通知', 'color' => '#73a68d'),
                            'keyword2' => array('value' => '【商户名称】' . $cashier_stores['name'], 'color' => '#73a68d'),
                            'remark' => array('value' => '恭喜,您的提现申请已经成功提交!')
                        );          
    m('message')->sendCustomNotice($openid, $msg);
    return show_json(1);
}

include $this->template('withdraw');
