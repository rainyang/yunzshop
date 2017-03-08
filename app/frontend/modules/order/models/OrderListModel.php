<?php

namespace app\frontend\modules\order\models;

use app\common\models\Order;

class OrderListModel extends Order
{
    /*
     * 获取所有状态的订单列表及订单商品信息 (不包括"已删除"的订单)
     */
    public static function getOrderList()
    {
        $orders = Order::with(['hasManyOrderGoods'=>function($query){
            return $query->select(['id','order_id','goods_id','goods_price','total','price'])
                ->with(['belongsToGood'=>function($query){
                    return $query->select(['id','price','title']);
                }]);
        }])->get(['id','status','order_sn','goods_price','price']);
        return $orders;
    }

    /*
     * 不同订单状态的订单列表及订单商品信息
     */
    public static function getRequestOrderList($status = '')
    {
        if($status === ''){
            return self::getOrderList();
        } else {
            return self::getOrderList()->where('status','=',$status);
        }
    }
}