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

$order_list = $_YZ->m('order')->getList(
    array(
        'id'=>intval($_GPC["id"]),
        'status'=>intval($_GPC["status"]),
        'paytype'=>intval($_GPC["paytype"]),
        'is_supplier_uid'=>$_YZ->isSupplier()
    )
);
if(count($order_list)==0){
    $_YZ->returnSuccess([],'暂无数据');
}

dump($order_list);
$_YZ->returnSuccess($order_list);