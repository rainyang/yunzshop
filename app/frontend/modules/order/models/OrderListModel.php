<?php

namespace app\frontend\modules\order\models;

use app\common\models\Order;

class OrderListModel extends Order
{
//    protected $hidden = ['pay_time'];

    /*
     * 获取所有状态的订单列表及订单商品信息 (不包括"已删除"的订单)
     */
    public static function getOrderList($pageSize)
    {
        $orders = Order::with(['hasManyOrderGoods'=>function($query){
            return $query->select(['order_id','goods_id','goods_price','total','price','thumb','title']);
        }])->paginate($pageSize);

        return $orders;
    }

    /*
     * 不同订单状态的订单列表及订单商品信息
     */
    public static function getRequestOrderList($status = '',$pageSize = 5)
    {
        if($status === ''){
            return self::getOrderList($pageSize);
        } else {
            return self::getOrderList($pageSize)->where('status','=',$status);
        }
    }
}