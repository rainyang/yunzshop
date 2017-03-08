<?php

namespace app\frontend\modules\order\models;

use app\common\models\Order;


class OrderDetailModel extends Order
{
    /*
     * 根据 Order_id 获取订单详情,包括订单内商品的详情
     */
    public static function getOrderDetail($orderId)
    {
        $orderModels = Order::with(['hasManyOrderGoods'=>function($query){
            return $query->select(['id','order_id','goods_id','goods_price','total','price','title','thumb']);
        }])->get(['id','order_sn'])->find($orderId);
        return $orderModels;
    }
}