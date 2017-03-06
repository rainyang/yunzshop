<?php

error_reporting(0);
require '../../../../../../framework/bootstrap.inc.php';
require '../../../../../../addons/sz_yi/defines.php';
require '../../../../../../addons/sz_yi/core/inc/functions.php';
require '../../../../../../addons/sz_yi/core/inc/plugin/plugin_model.php';
global $_W, $_GPC;
print_r($_W);exit;
set_time_limit(0);
// $get_data = file_get_contents("php://input"); 
// $data = json_decode($get_data, true);
$data = array (
    'ordernum' => $_GPC['out_order_id'],
    'state' => $_GPC['status'],
    'desc' => $_GPC['err_desc'],
    'completion_time' => $_GPC['completion_time'],
    );
if(!empty($data)){
    file_put_contents(IA_ROOT."/data_backurl_log.txt", print_r($data,true),FILE_APPEND);
    file_put_contents(IA_ROOT."/back_return.txt", print_r($data,true),FILE_APPEND);
}else{
    file_put_contents(IA_ROOT."/data_backurl_log.txt", "没收到回调信息...",FILE_APPEND);
    file_put_contents(IA_ROOT."/back_return.txt", "没收到回调信息...",FILE_APPEND);
}


$order = pdo_fetch("SELECT id,openid,redprice,uniacid,status,price FROM ". tablename("sz_yi_order") . "WHERE ordersn = '".$data['ordernum']."'");
$_W['uniacid'] = $order['uniacid'];
/*
 *  平台返回给下游状态码
 *  3:成功,4:失败。
 */
if($data['state'] == 3 && $order['status'] != 3){
    pdo_update('sz_yi_order', array(
        'status' => 3,
        'finishtime' => time()
        ), array(
            'ordersn' => $data['ordernum']
    ));
    $_var_156 = array('keyword1' => array('value' => '手机流量充值成功', 'color' => '#73a68d'), 'keyword2' => array('value' => '[订单编号]'.$data['ordernum'], 'color' => '#73a68d'),'remark' => array('value' => '您购买的流量已经充值成功.关注订阅号《优惠一线》优惠早知道.'));
    if($order['status'] != 3){
        if(p('redpack') && !empty($order['redprice'])){
            p('redpack')->sendredpack($order['openid'], $order['redprice']*100, $order['uniacid'],$order['id']);
        }
    }
    m('message')->sendCustomNotice($order['openid'], $_var_156);
    return "0";
    
}else if($data['state'] == 4 && $order['status'] != 3){
    $_var_156 = array('keyword1' => array('value' => '手机流量充值失败', 'color' => '#73a68d'), 'keyword2' => array('value' => '[订单编号]'.$data['ordernum'], 'color' => '#73a68d'),'remark' => array('value' => '您购买的手机流量充值失败，失败原因：'.urldecode($data['desc'])));
    pdo_update('sz_yi_order', array(
        'remark' => "流量充值失败,失败原因:".urldecode($data['desc'])
        ), array(
            'ordersn' => $data['ordernum']
    ));
    m('message')->sendCustomNotice($order['openid'], $_var_156);
    /**
     *      自动退款
     **/
    $setdata = pdo_fetch("select * from " . tablename('sz_yi_sysset') . ' where uniacid=:uniacid limit 1', array(
    ':uniacid' => $order['uniacid']));
    $set     = unserialize($setdata['sets']);
    //file_put_contents(IA_ROOT."/fail_test_1.txt", print_r($set,true));
    //print_R($set);exit;
    if ($set['data']['auto_refund'] == 1) {
        $refund = pdo_fetch("SELECT * FROM " . tablename('sz_yi_order_refund') . " WHERE orderid = :orderid AND uniacid = :uniacid",array(':orderid' => $order['id'],':uniacid' => $order['uniacid']));
        if(empty($refund)){
            $refundno= m("common")->createNO("order_refund", "refundno", "SR");
            $order_refund = array(
                "uniacid" => $order['uniacid'],
                "orderid" => $order['id'],
                "refundno" => $refundno,
                "price" => $order['price'],
                "reason" => "自动退款",
                "content" => urldecode($data['desc']),
                "createtime" => time(),
                "status" => 1,
                "refundtype" => 1,
                );
            pdo_insert('sz_yi_order_refund',$order_refund);
            
            $returnid = pdo_insertid();
            //file_put_contents(IA_ROOT."/fail_test_2.txt", print_r($order_refund,true));
            pdo_update('sz_yi_order', array(
                'status' => -1,
                'refundtime' => time(),
                ), array(
                    'id' => $order['id']
            ));
            if($returnid){
                file_put_contents(IA_ROOT."/data_backurl_refund_log.txt", print_r($order_refund,true),FILE_APPEND);
                //m("finance")->pay($order['openid'], 1, $order['price'] * 100, $refundno,"流量充值退款: " . $order['price'] . "元 订单号: " . $data['ordernum'],$order['uniacid']);
                $isrefund= m("finance")->refund($order['openid'], $data['ordernum'], $refundno, $order['price'] * 100, $order['price'] * 100,$order['uniacid']);

                if($isrefund){
                    file_put_contents(IA_ROOT."/data_backurl_refund_price_log.txt", "订单".$data['ordernum']."充值失败退款成功...",FILE_APPEND);
                    $auto_refund_mess = array(
                    'keyword1'  => array('value' => '流量充值失败自动退款成功', 'color' => '#73a68d'),
                    'keyword2'  => array('value' => '[订单编号]'.$data['ordernum'], 'color' => '#73a68d'),
                    'keyword3'  => array('value' => '[退单编号]'.$refundno,'color' => '#73a68d'),
                    'keyword4'  => array('value' => '[退款金额]'.$order['price'],'color' => '#73a68d'),
                    'keyword4'  => array('value' => '[退款方式]微信钱包','color' => '#73a68d'),
                    'remark'    => array('value' => '您的流量充值失败，已经自动给您退款成功，退款到您的微信钱包，请根据订单编号查看确认退款金额是否正确！')
                    );
                    m('message')->sendCustomNotice($order['openid'], $auto_refund_mess);
                }
                
            }else{
                $auto_refund_mess = array(
                    'keyword1'  => array('value' => '流量充值失败自动退款失败', 'color' => '#73a68d'),
                    'keyword2'  => array('value' => '[订单编号]'.$data['ordernum'], 'color' => '#73a68d'),
                    'keyword3'  => array('value' => '[退单编号]'.$refundno,'color' => '#73a68d'),
                    'keyword4'  => array('value' => '[退款金额]'.$order['price'],'color' => '#73a68d'),
                    'keyword4'  => array('value' => '[退款方式]微信钱包','color' => '#73a68d'),
                    'remark'    => array('value' => '您的流量充值失败，自动给您退款失败，如果您收到此消息请您联系管理员！')
                    );
                    m('message')->sendCustomNotice($order['openid'], $auto_refund_mess);
            }
            
            
        }
    }
    return "0";
    //file_put_contents(IA_ROOT."/fail_test_end.txt", print_r($auto_refund_mess,true));

}




   