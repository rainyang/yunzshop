<?php
/*
require '../../../../../framework/bootstrap.inc.php';
require '../../../../../addons/sz_yi/defines.php';
require '../../../../../addons/sz_yi/core/inc/functions.php';
require '../../../../../addons/sz_yi/core/inc/plugin/plugin_model.php';
 */
global $_W, $_GPC;
//ignore_user_abort();
set_time_limit(0);
//echo $_W['uniacid'];
$sets = pdo_fetchall('select uniacid from ' . tablename('sz_yi_sysset'));
foreach ($sets as $set) {
    $_W['uniacid'] = $set['uniacid'];
    if (empty($_W['uniacid'])) {
        continue;
    }
    $trade = m('common')->getSysset('trade', $_W['uniacid']);
    $days = intval($trade['closeorder']);
    if ($days <= 0) {
        continue;
    }
    $daytimes = 60*60*24;
    //未申请退款订单
    $orders = pdo_fetchall('select id, price,openid,ordersn from ' . tablename('sz_yi_order') . " where  uniacid={$_W['uniacid']} and status=1
     and paytype<>3  and createtime + {$daytimes} <=unix_timestamp() ");
    foreach ($orders as $value) {
        $refund = pdo_fetch("SELECT * FROM " . tablename('sz_yi_order_refund') . " WHERE orderid = :orderid AND 
        uniacid = :uniacid",array(':orderid' => $value['id'],':uniacid' => $_W['uniacid']));
        if(empty($refund)){
            $refundno= m("common")->createNO("order_refund", "refundno", "SR");
            $order_refund = array(
                "uniacid" => $_W['uniacid'],
                "orderid" => $value['id'],
                "refundno" => $refundno,
                "price" => $value['price'],
                "reason" => "自动退款",
                "content" => '自动退款',
                "createtime" => time(),
                "status" => 1,
                "refundtype" => 1,
            );
            pdo_insert('sz_yi_order_refund',$order_refund);

            $returnid = pdo_insertid();
            pdo_update('sz_yi_order', array(
                'status' => -1,
                'refundtime' => time(),
            ), array(
                'id' => $value['id']
            ));
            if($returnid){
                $isrefund= m("finance")->refund($value['openid'], $value['ordersn'], $refundno, $value['price'] * 100,
                    $value['price'] * 100,$_W['uniacid']);

                if($isrefund){
                    $auto_refund_mess = array(
                        'keyword1'  => array('value' => '订单发货失败自动退款', 'color' => '#73a68d'),
                        'keyword2'  => array('value' => '[订单编号]'.$value['ordersn'], 'color' => '#73a68d'),
                        'keyword3'  => array('value' => '[退单编号]'.$refundno,'color' => '#73a68d'),
                        'keyword4'  => array('value' => '[退款金额]'.$value['price'],'color' => '#73a68d'),
                        'keyword4'  => array('value' => '[退款方式]微信钱包','color' => '#73a68d'),
                        'remark'    => array('value' => '您的订单发货失败，已经自动给您退款成功，退款到您的微信钱包，请根据订单编号查看确认退款金额是否正确！')
                    );
                    m('message')->sendCustomNotice($value['openid'], $auto_refund_mess);
                }

            }else{
                $auto_refund_mess = array(
                    'keyword1'  => array('value' => '订单发货失败自动退款失败', 'color' => '#73a68d'),
                    'keyword2'  => array('value' => '[订单编号]'.$value['ordersn'], 'color' => '#73a68d'),
                    'keyword3'  => array('value' => '[退单编号]'.$refundno,'color' => '#73a68d'),
                    'keyword4'  => array('value' => '[退款金额]'.$value['price'],'color' => '#73a68d'),
                    'keyword4'  => array('value' => '[退款方式]微信钱包','color' => '#73a68d'),
                    'remark'    => array('value' => '您的订单发货失败，自动给您退款失败，如果您收到此消息请您联系管理员！')
                );
                m('message')->sendCustomNotice($value['openid'], $auto_refund_mess);
            }


        }
    }
    //已申请退款订单

    $refund_return = pdo_fetchall("SELECT * FROM " . tablename('sz_yi_order_refund') . " WHERE status = 0 AND
        uniacid = :uniacid",array(':uniacid' => $_W['uniacid']));
    foreach ($refund_return as $value) {

        pdo_update('sz_yi_order_refund', array(
            'status' => 1,
            "refundtype" => 1,
        ), array(
            'id' => $value['id']
        ));
        $order = pdo_fetch('select id, price,openid,ordersn from ' . tablename('sz_yi_order') . " where  id = :id",array(":id" => $value['orderid']));
        $isrefund= m("finance")->refund($order['openid'], $order['ordersn'], $refund_return['refundno'], $order['price'] * 100,
            $order['price'] * 100,$_W['uniacid']);

        if($isrefund){
            $auto_refund_mess = array(
                'keyword1'  => array('value' => '订单发货失败自动退款', 'color' => '#73a68d'),
                'keyword2'  => array('value' => '[订单编号]'.$order['ordersn'], 'color' => '#73a68d'),
                'keyword3'  => array('value' => '[退单编号]'.$refund_return['refundno'],'color' => '#73a68d'),
                'keyword4'  => array('value' => '[退款金额]'.$order['price'],'color' => '#73a68d'),
                'keyword4'  => array('value' => '[退款方式]微信钱包','color' => '#73a68d'),
                'remark'    => array('value' => '您的订单发货失败，已经自动给您退款成功，退款到您的微信钱包，请根据订单编号查看确认退款金额是否正确！')
            );
            m('message')->sendCustomNotice($value['openid'], $auto_refund_mess);
        }
    }

    /*$sql = "SELECT o.id FROM `ims_sz_yi_order` o left join ( select rr.id,rr.orderid,rr.status from `ims_sz_yi_order_refund` rr left join `ims_sz_yi_order` ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id left join `ims_sz_yi_member` m on m.openid=o.openid and m.uniacid = o.uniacid left join `ims_sz_yi_member_address` a on o.addressid = a.id left join `ims_sz_yi_member` sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid left join `ims_sz_yi_saler` s on s.openid = o.verifyopenid and s.uniacid=o.uniacid WHERE o.uniacid = 4 and o.deleted=0 and o.status=-1 and o.refundtime=0";
    $order = pdo_fetchall($sql);
    foreach ($order as $value) {
        pdo_delete('sz_yi_order', array('id' => $value['id']));
    }*/
}
echo "ok...";
