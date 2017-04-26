<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/4/11
 * Time: 下午6:27
 */

namespace app\common\services\finance;

use Setting;

class CalculationPointService
{
    public static function calcuationPointByGoods($order_goods_model)
    {
        $point_set = Setting::get('point.set');
        $point_data = [];
        //todo 如果等于0  不赠送积分
        if ($order_goods_model->hasOneGoods->hasOneSale->point === 0) {
            return $point_data;
        }
        //todo 如果不等于空，按商品设置赠送积分，否则按统一设置赠送积分
        if (!empty($order_goods_model->hasOneGoods->hasOneSale->point)) {
            if (strexists($order_goods_model->hasOneGoods->hasOneSale->point, '%')) {
                $point_data['point'] = floatval(str_replace('%', '', $order_goods_model->hasOneGoods->hasOneSale->point) / 100 * $order_goods_model->goods_price * $order_goods_model->total);
            } else {
                $point_data['point'] = $order_goods_model->hasOneGoods->hasOneSale->point * $order_goods_model->total;
            }
            $point_data['remark'] = '购买商品[' . $order_goods_model->hasOneGoods->title .']赠送[$order_goods->hasOneGoods->hasOneSale->point]积分！';
        } else if (!empty($point_set['give_point'] && $point_set['give_point'])) {
            if (strexists($point_set['give_point'], '%')) {
                $point_data['point'] = floatval(str_replace('%', '', $point_set['give_point']) / 100 * $order_goods_model->goods_price * $order_goods_model->total);
            } else {
                $point_data['point'] = $point_set['give_point'] * $order_goods_model->total;
            }
            $point_data['remark'] = '购买商品[统一设置]赠送[$order_goods->hasOneGoods->hasOneSale->point]积分！';
        }
        return $point_data;
    }

    public static function calcuationPointByOrder($order_model)
    {
        $point_set = Setting::get('point.set');
        $point_data = [];
        if (isset($point_set['enoughs'])) {
            foreach (collect($point_set['enoughs'])->sortByDesc('enough') as $enough) {
                if ($order_model->price >= $enough['enough'] && $enough['give'] > 0) {
                    $point_data['point'] = $enough['give'];
                    $point_data['remark'] = '订单[' . $order_model->order_sn . ']消费满[' . $enough['enough'] . ']元赠送[' . $enough['give'] . ']积分';
                }
            }
        } else if (!empty($point_set['enough_money']) && !empty($point_set['enough_point'])) {
            if ($order_model->price >= $point_set['enough_money'] && $point_set['enough_point'] > 0) {
                $point_data['point'] = $point_set['enough_point'];
                $point_data['remark'] = '订单[' . $order_model->order_sn . ']消费满[' . $point_set['enough_money'] . ']元赠送[' . $point_data['point'] . ']积分';
            }
        }
        return $point_data;
    }
}