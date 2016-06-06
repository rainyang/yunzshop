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
$orderids = array();
foreach ($cashier_order as $order) {
    $orderids[] = $order['order_id'];
}
$totalprices = 0;
if ($orderids) {
    // 累计支付金额
    $totalprices = pdo_fetch('SELECT SUM(price) AS tprice FROM ' . tablename('sz_yi_order') . ' WHERE uniacid = ' . $_W['uniacid'] . ' AND id IN (' . implode(',', $orderids) . ') AND status = 3');
    $totalprices = $totalprices['tprice']*(100-$store['settle_platform'])/100;
    // 已经提现的金额
    $totalwithdraw = pdo_fetch('SELECT money FROM ' . tablename('sz_yi_cashier_withdraw') . ' WHERE uniacid = ' . $_W['uniacid'] . ' AND cashier_store_id = ' . $store['id']);
    $totalwithdraw = $totalwithdraw['money'];
    $totalprices = number_format($totalprices - $totalwithdraw, 2);
}

if ($operation == 'display' && $_W['isajax']) {
    $store['totalprices'] = $totalprices;
    show_json(1, array(
        'store'  => $store,
        'noinfo' => empty($member['realname'])
    ));
} else if ($operation == 'submit' && $_W['ispost']) {
    $money = floatval($_GPC['money']);
    if (empty($money)) {
        show_json(0, '申请金额为空!');
    }
    if ($money <= 0) {
        show_json(0, '提现金额不能小于0元!');
    }
    if ($money > $totalprices) {
        show_json(0, '提现金额过大!');
    }
    $withdraw_no = m('common')->createNO('cashier_withdraw', 'withdraw_no', 'CW');
    $data = array(
        'uniacid'           => $_W['uniacid'],
        'withdraw_no'       => $withdraw_no,
        'openid'            => $openid,
        'cashier_store_id'  => $store['id'],
        'money'             => $money,
        'status'            => 0
    );
    pdo_insert('sz_yi_cashier_withdraw', $data);
    show_json(1);
}

include $this->template('withdraw');
