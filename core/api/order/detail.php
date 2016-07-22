<?php
/**
 * 管理后台APP API订单接口
 *
 * PHP version 5.6.15
 *
 * @package   订单模块
 * @author    shenyang <shenyang@yunzshop.com>
 * @version   v1.0
 */
//$api->validate('username','password');
$_YZ->ca("order.view.status_1|order.view.status0|order.view.status1|order.view.status2|order.view.status3|order.view.status4|order.view.status5");
$para = $_YZ->getPara();
$order_model = new \model\api\order();
$order_info = $order_model->getInfo(array(
        'id'=>$para["order_id"],
        'uniacid'=>$para["uniacid"]
    ),
    'ordersn,status,price,id as order_id,openid'
);
$order_info['goods'] = $order_model->getOrderGoods($para["order_id"],$para["uniacid"]);

$member_model = new \model\api\member();
$member = $member_model->getInfo(array(
    'openid'=>$order_info["openid"],
    'uniacid'=>$para["uniacid"]
),
    'id as member_id,realname,weixin,mobile,nickname,avatar');

$dispatch = pdo_fetch("SELECT * FROM " . tablename("sz_yi_dispatch") . " WHERE id = :id and uniacid=:uniacid", array(
    ":id" => $order_info["dispatchid"],
    ":uniacid" => $_W["uniacid"]
));
if (empty($order_info["addressid"])) {
    $user = unserialize($order_info["carrier"]);
} else {
    $user = iunserializer($order_info["address"]);
    if (!is_array($user)) {
        $user = pdo_fetch("SELECT * FROM " . tablename("sz_yi_member_address") . " WHERE id = :id and uniacid=:uniacid", array(
            ":id" => $order_info["addressid"],
            ":uniacid" => $_W["uniacid"]
        ));
    }
    $address_info = $user["address"];
    $user["address"] = $user["province"] . " " . $user["city"] . " " . $user["area"] . " " . $user["address"];
    $order_info["addressdata"] = array(
        "realname" => $user["realname"],
        "mobile" => $user["mobile"],
        "address" => $user["address"],
    );
}

$goods = pdo_fetchall("SELECT g.*, o.goodssn as option_goodssn, o.productsn as option_productsn,o.total,g.type,o.optionname,o.optionid,o.price as orderprice,o.realprice,o.changeprice,o.oldprice,o.commission1,o.commission2,o.commission3,o.commissions{$diyformfields} FROM " . tablename("sz_yi_order_goods") . " o left join " . tablename("sz_yi_goods") . " g on o.goodsid=g.id " . " WHERE o.orderid=:orderid and o.uniacid=:uniacid", array(
    ":orderid" => $id,
    ":uniacid" => $_W["uniacid"]
));

$res=compact('order_info','member','dispatch');
dump($res);
$_YZ->returnSuccess($res);