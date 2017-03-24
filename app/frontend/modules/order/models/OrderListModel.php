<?php

namespace app\frontend\modules\order\models;

use app\common\models\Order;

class OrderListModel extends Order
{
    protected $hidden = ['uniacid','status','create_time','is_deleted','is_member_deleted',
                        'finish_time','pay_time',',send_time','send_time','uid',
                        'cancel_time','created_at','updated_at','deleted_at']; //在 Json 中隐藏的字段

    /*
     * 获取所有状态的订单列表及订单商品信息 (不包括"已删除"的订单)
     */
    public static function getOrderList($uid)
    {
        $orders = self::with(['hasManyOrderGoods'=>function($query){
            return $query->select(['order_id','goods_id','goods_price','total','price','thumb','title']);
        }])->where('uid','=',$uid);

        return $orders;
    }

    /*
     * 不同订单状态的订单列表及订单商品信息
     */
    public static function getRequestOrderList($status = '', $uid)
    {
        if($status === ''){
            return self::getOrderList($uid);
        } else {
            return self::getOrderList($uid)->where('status','=',$status);
        }
    }
}