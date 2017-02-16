<?php
global $_W, $_GPC;
set_time_limit(0);
$data = array (
    'ordernum' => trim($_GPC['out_order_id']) !== false ? trim($_GPC['out_order_id']) : '',
    'phone_no' => trim($_GPC['phone_no']) !== false ? trim($_GPC['phone_no']) : '',
    'state' => intval($_GPC['status']) !== false ? intval($_GPC['status']) : '',
    'desc' => intval($_GPC['err_desc']) !== false ? intval($_GPC['err_desc']) : '',
    'completion_time' => intval($_GPC['report_time']) !== false ? intval($_GPC['report_time']) : time(),
    );
$this->model->rechargeLog('api_back_data', print_r($data, true));
$order = $this->model->getOrderByOrdersn($data['ordernum']);
$_W['uniacid'] = $order['uniacid'];
$set = $this->model->getSet();
$notice_set = $set['tm'];
/*
 *  平台返回给下游状态码
 *  3:成功,4:失败。
 */
if ($data['state'] == 3 && $order['status'] != 3) {
    pdo_update('sz_yi_order', array(
        'status' => 3,
        'finishtime' => time()
        ), array(
            'ordersn' => $order['ordersn']
    ));
    $message = array(
        'keyword1' => array('value' => '手机流量充值成功', 'color' => '#73a68d'),
        'keyword2' => array('value' => '[订单编号]' . $order['ordersn'], 'color' => '#73a68d'),
        'keyword3' => array('value' => '[充值号码]' . $data['phone_no'], 'color' => '#73a68d'),
        'remark' => array('value' => $notice_set['recharge_success'])
    );
    m('message')->sendCustomNotice($order['openid'], $message);
    if ($order['status'] != 3) {
        if (p('redpack') && !empty($order['redprice'])) {
            p('redpack')->sendredpack($order['openid'], $order['redprice']*100, $order['uniacid'], $order['id']);
        }
    }
} else if ($data['state'] == 4 && $order['status'] != 3) {
    $remark_data = array(
        'uniacid' => $_W['uniacid'],
        'orderid' => $order['id'],
        'remark' =>  "流量充值失败,失败原因: " . urldecode($data['desc']),
        'createtime' => time()
    );
    pdo_insert('sz_yi_recharge_remark', $remark_data);
    $message = array(
        'keyword1' => array('value' => '手机流量充值失败', 'color' => '#73a68d'),
        'keyword2' => array('value' => '[订单编号]' . $order['ordersn'], 'color' => '#73a68d'),
        'keyword3' => array('value' => '[失败原因]' . urldecode($data['desc']), 'color' => '#73a68d'),
        'remark' => array('value' => $notice_set['recharge_fail'])
    );
    m('message')->sendCustomNotice($order['openid'], $message);
    $refund = pdo_fetch("SELECT * FROM " . tablename('sz_yi_order_refund') . "
    WHERE orderid = :orderid AND uniacid = :uniacid",
        array(
            ':orderid' => $order['id'],
            ':uniacid' => $_W['uniacid']
        )
    );
    if (empty($refund)) {
        $refunddata = array(
            'orderid' => $order['id'],
            'price' => $order['price'],
            'content' => urldecode($data['desc']),
            'openid' => $order['openid'],
            'pay_ordersn' => $order['pay_ordersn'],
            'ordersn' => $order['ordersn']
        );
        $this->model->autoRefund($refunddata);
    }
}
echo "0";



   