<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 16/4/27
 * Time: 下午1:35
 */

if (!defined('IN_IA')) {
    exit('Access Denied');
}

global $_W, $_GPC;

$uniacid        = $_W['uniacid'];

require_once('../addons/sz_yi/plugin/pingpp/init.php');

$input_data=json_decode(file_get_contents('php://input'),true);

/*$input_data = json_decode('{
    "id": "evt_ugB6x3K43D16wXCcqbplWAJo",
    "created": 1427555101,
    "livemode": true,
    "type": "charge.succeeded",
    "data": {
        "object": {
            "id": "ch_1eHOO4XrzfrTn1KGuPGGuvzD",
            "object": "charge",
            "created": 1427555076,
            "livemode": true,
            "paid": true,
            "refunded": false,
            "app": "app_1Gqj58ynP0mHeX1q",
            "channel": "upacp",
            "order_no": "22910252521",
            "client_ip": "127.0.0.1",
            "amount": 100,
            "amount_settle": 0,
            "currency": "cny",
            "subject": "Your Subject",
            "body": "Your Body",
            "extra": {},
            "time_paid": 1427555101,
            "time_expire": 1427641476,
            "time_settle": null,
            "transaction_no": "1224524301201505066067849274",
            "refunds": {
                "object": "list",
                "url": "/v1/charges/ch_L8qn10mLmr1GS8e5OODmHaL4/refunds",
                "has_more": false,
                "data": []
            },
            "amount_refunded": 0,
            "failure_code": null,
            "failure_msg": null,
            "metadata": {},
            "credential": {},
            "description": null
        }
    },
    "object": "event",
    "pending_webhooks": 0,
    "request": "iar_qH4y1KbTy5eLGm1uHSTS00s"
}',true);*/
do{
    if(!isset($input_data['id'])){
        $res['status'] = 500;
        $res['msg'] = "Internal Server Error";
        echo '事件ID为空';
        break;
    }

    // 对异步通知做处理
    if (!isset($input_data['type'])) {
        header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request');
        exit("fail");
    }
    switch ($input_data['type']) {
        case "charge.succeeded":
            $pay_info = $input_data['data']['object'];

            if($pay_info['paid'] == 1){
                $order_data['pay_time']=$pay_info['time_paid'];//时间戳
                $order_data['pay_id'] = $pay_info['id'];
                $order_data['order_id'] = $pay_info['order_no'];

                if ($pay_info['channel'] == 'wx') {
                    $pay_type = 'app_wechat';
                    $pay_type_num = 27;

                } elseif ($pay_info['channel'] == 'alipay') {
                    $pay_type = 'app_alipay';
                    $pay_type_num = 28;
                }

                $paylog = "\r\n-------------------------------------------------\r\n";
                $paylog .= "orderno: " . $pay_info['order_no'] . "\r\n";
                $paylog .= "paytype: $pay_type\r\n";
                $paylog .= "data: " . json_encode($input_data) . "\r\n";
                m('common')->paylog($paylog);

                if (substr($pay_info['order_no'],0,2) == 'RC') {
                    $log = pdo_fetch('SELECT * FROM ' . tablename('sz_yi_member_log') . ' WHERE `logno`=:logno and `uniacid`=:uniacid limit 1', array(
                        ':uniacid' => $uniacid,
                        ':logno' => $pay_info['order_no']
                    ));
                    if (!empty($log) && empty($log['status'])) {
                        pdo_update('sz_yi_member_log', array(
                            'status' => 1,
                            'rechargetype' => $pay_type
                        ), array(
                            'id' => $log['id']
                        ));
                        m('member')->setCredit($log['openid'], 'credit2', $log['money']);
                        m('member')->setRechargeCredit($log['openid'], $log['money']);
                        if (p('sale')) {
                            p('sale')->setRechargeActivity($log);
                        }
                        m('notice')->sendMemberLogMessage($log['id']);
                    }
                } else {
                    $ordersn_general          = pdo_fetchcolumn('select ordersn_general from ' . tablename('sz_yi_order') . ' where (ordersn=:ordersn or ordersn_general=:ordersn) and uniacid=:uniacid  limit 1', array(
                        ':ordersn' => $pay_info['order_no'],
                        ':uniacid' => $uniacid
                    ));

                    $order_info    = pdo_fetch('select id, ordersn_general from ' . tablename('sz_yi_order') . ' where ordersn_general=:ordersn_general and uniacid=:uniacid limit 1', array(
                        ':ordersn_general' => $ordersn_general,
                        ':uniacid' => $uniacid
                    ));

                    pdo_query('update ' . tablename('sz_yi_order') . ' set paytype='. $pay_type_num .', trade_no="'. $pay_info['id'] .'" where ordersn_general=:ordersn_general and uniacid=:uniacid ', array(
                        ':uniacid' => $uniacid,
                        ':ordersn_general' => $order_info['ordersn_general']
                    ));

                    $log = pdo_fetch('SELECT * FROM ' . tablename('core_paylog') . ' WHERE `uniacid`=:uniacid AND `module`=:module AND `tid`=:tid limit 1', array(
                        ':uniacid' => $uniacid,
                        ':module' => 'sz_yi',
                        ':tid' => $order_info['ordersn_general']
                    ));

                    if ($log['status'] != 1) {
                        $record           = array();
                        $record['status'] = '1';
                        $record['type']   = 'alipay';
                        pdo_update('core_paylog', $record, array(
                            'plid' => $log['plid']
                        ));
                        $ret            = array();
                        $ret['result']  = 'success';
                        $ret['type']    = $pay_type;
                        $ret['from']    = 'return';
                        $ret['tid']     = $log['tid'];
                        $ret['user']    = $log['openid'];
                        $ret['fee']     = $log['fee'];
                        $ret['weid']    = $log['weid'];
                        $ret['uniacid'] = $log['uniacid'];
                        $this->payResult($ret);

                        m('notice')->sendOrderMessage($order_info['id']);
                        echo '成功';
                        $res['status'] = 200;
                        $res['msg'] = "ok";
                    }
                }
            }
            break;
        case "refund.succeeded":
            $refund = $input_data['data']['object'];

            if ($refund['succeed'] == true && $refund['status'] == 'succeeded') {

                $ordersn_general = pdo_fetchcolumn('select ordersn_general from ' . tablename('sz_yi_order') . ' where (pay_ordersn=:ordersn or ordersn_general=:ordersn) and uniacid=:uniacid limit 1', array(
                    ':ordersn' => $refund['charge_order_no'],
                    ':uniacid' => $uniacid
                ));


                $order_info = pdo_fetch("SELECT * FROM " . tablename('sz_yi_order') . " WHERE uniacid=:uniacid AND ordersn_general=:ordersn_general", array(
                        ':uniacid'=> $uniacid,
                        ':ordersn_general'=> $ordersn_general
                ));


                //if ($order_info['paytype'] == 28) {

                    $shopset = m('common')->getSysset('shop');

                    $goods = pdo_fetchall('SELECT g.id,g.credit, o.total,o.realprice FROM ' . tablename('sz_yi_order_goods') . ' o left join ' . tablename('sz_yi_goods') . ' g on o.goodsid=g.id ' . ' WHERE o.orderid=:orderid and o.uniacid=:uniacid', array(
                        ':orderid' => $order_info['id'],
                        ':uniacid' => $uniacid
                    ));
                    $credits = 0;
                    foreach ($goods as $g) {
                        $gcredit = trim($g['credit']);
                        if (!empty($gcredit)) {
                            if (strexists($gcredit, '%')) {
                                $credits += intval(floatval(str_replace('%', '', $gcredit)) / 100 * $g['realprice']);
                            } else {
                                $credits += intval($g['credit']) * $g['total'];
                            }
                        }
                    }


                    if ($credits > 0) {
                        m('member')->setCredit($order_info['openid'], 'credit1', -$credits, array(
                            0,
                            $shopset['name'] . "退款扣除积分: {$credits} 订单号: " . $order_info['ordersn']
                        ));
                    }
                    if ($order_info['deductcredit'] > 0) {
                        m('member')->setCredit($order_info['openid'], 'credit1', $order_info['deductcredit'], array(
                            '0',
                            $shopset['name'] . "购物返还抵扣积分 积分: {$order_info['deductcredit']} 抵扣金额: {$order_info['deductprice']} 订单号: {$order_info['ordersn']}"
                        ));
                    }

                    if ($order_info['deductyunbimoney'] > 0) {
                        p('yunbi')->setVirtualCurrency($order_info['openid'],$order_info['deductyunbi']);
                        //虚拟币抵扣记录
                        $data_log = array(
                            'id'            => '',
                            'openid'        => $order_info['openid'],
                            'credittype'    => 'virtual_currency',
                            'money'         => $order_info['deductyunbi'],
                            'remark'        => "购物返还抵扣".$yunbiset['yunbi_title']." ".$yunbiset['yunbi_title'].": {$order_info['deductyunbi']} 抵扣金额: {$order_info['deductyunbimoney']} 订单号: {$order_info['ordersn']}"
                        );
                        p('yunbi')->addYunbiLog($_W["uniacid"],$data_log,'4');
                    }



                    if ($order_info['deductcredit2'] > 0) {
                        m('member')->setCredit($order_info['openid'], 'credit2', $order_info['deductcredit2'], array(
                            '0',
                            $shopset['name'] . "购物返还抵扣余额 积分: {$order_info['deductcredit2']} 订单号: {$order_info['ordersn']}"
                        ));
                    }



                    $data['reply']      = '';
                    $data['status']     = 1;
                    $data['refundtype'] = 3;
                    $data['price']      = $refund['amount'] * 0.01;
                    $data['refundtime'] = time();
                    pdo_update('sz_yi_order_refund', $data, array(
                        'id' => $order_info['refundid']
                    ));
                    m('notice')->sendOrderMessage($item['id'], true);

                    pdo_update('sz_yi_order', array(
                        'refundstate' => 0,
                        'status' => -1,
                        'refundtime' => time()
                    ), array(
                        'id' => $order_info['id'],
                        'uniacid' => $uniacid
                    ));
                //}
            }

            header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK');
            break;
        default:
            header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request');
            break;
    }




}while(0);
header("HTTP/1.1 ".$res['status']." ".$res['msg']);