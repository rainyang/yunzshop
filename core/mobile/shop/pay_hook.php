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
    $pay_info = $input_data['data']['object'];


    if($pay_info['paid'] == 1){
        $order_data['pay_time']=$pay_info['time_paid'];//时间戳
        $order_data['pay_id'] = $pay_info['id'];
        $order_data['order_id'] = $pay_info['order_no'];

        if (substr($pay_info['order_no'],0,2) == 'RC') {
            if ($pay_info['channel'] == 'wx') {
                $pay_type = 'wechat';
            } elseif ($pay_info['channel'] == 'alipay') {
                $pay_type = 'alipay';
            }

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

            $order_info = pdo_fetch("SELECT * FROM " . tablename('sz_yi_order') . " WHERE uniacid=:uniacid AND ordersn=:ordersn", array(
                'uniacid'=> $uniacid,
                'ordersn'=> $pay_info['order_no']
            ));

            if($order_info['status']!=0){
                $res['status'] = 500;
                $res['msg'] = "Internal Server Error";
                echo '支付状态不正确';
                break;
            }

            $pay_type = array(
                "wx" => 21,
                "alipay" => 22
            );

            if(!pdo_update('sz_yi_order',array('status'=>1,'paytype'=>$pay_type[$pay_info['channel']]),array('ordersn'=>$pay_info['order_no']))){
                echo '订单状态改变失败';
                $res['status'] = 500;
                $res['msg'] = "Internal Server Error";
                break;
            }else{
                m('notice')->sendOrderMessage($order_info['id']);
                echo '成功';
                $res['status'] = 200;
                $res['msg'] = "ok";
            }
        }


    }
}while(0);
header("HTTP/1.1 ".$res['status']." ".$res['msg']);