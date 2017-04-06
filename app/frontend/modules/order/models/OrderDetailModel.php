<?php

namespace app\frontend\modules\order\models;

use app\common\models\Order;


class OrderDetailModel extends Order
{
//    protected $hidden = ['id','uniacid','status','is_deleted','is_member_deleted','created_at','updated_at','deleted_at'];

    /*
     * 根据 Order_id 获取订单详情,包括订单内商品的详情
     */
    public static function getOrderDetail($orderId)
    {
        $orderModels = self::with(['hasManyOrderGoods'=>function($query){
            return $query->select(['order_id','goods_option_title','goods_id','goods_price','total','price','title','thumb']);
        }])->select(['id','uid','order_sn','price','goods_price','create_time','finish_time','pay_time','send_time','cancel_time','dispatch_type_id','status'])->find($orderId);
        return $orderModels;
    }
}