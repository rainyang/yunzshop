<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/7
 * Time: 下午2:59
 */

namespace app\backend\modules\order\models;


class Order extends \app\common\models\Order
{
    public function getOrders()
    {

    }

    public function scopeSearch($order_builder, $params)
    {
//dd($params);
        if (array_get($params, 'ambiguous.field', '') && array_get($params, 'ambiguous.string', '')) {
            //订单
            if ($params['ambiguous']['field'] == 'order') {
                $order_builder->searchLike($params['ambiguous']['string']);
            }
            //用户
            if ($params['ambiguous']['field'] == 'member') {
                $order_builder->whereHas('belongsToMember', function ($query) use ($params) {
                    $query->searchLike($params['ambiguous']['string']);
                });
            }
            //订单商品
            if ($params['ambiguous']['field'] == 'order_goods') {
                $order_builder->whereHas('hasManyOrderGoods', function ($query) use ($params) {
                    $query->searchLike($params['ambiguous']['string']);
                });
            }
        }
        //支付方式
        if (array_get($params, 'pay_type', '')) {
            $order_builder->where('pay_type_id', $params['pay_type']);
        }
        //操作时间范围

        if (array_get($params, 'time_range.field', '') && array_get($params, 'time_range.start', 0) && array_get($params, 'time_range.end', 0)) {
            $range = [strtotime($params['time_range']['start']), strtotime($params['time_range']['end'])];
            $order_builder->whereBetween($params['time_range']['field'], $range);
        }
        return $order_builder;
    }

}