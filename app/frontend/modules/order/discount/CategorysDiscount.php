<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/20
 * Time: 21:00
 */

namespace app\frontend\modules\order\discount;

use app\frontend\modules\orderGoods\models\PreOrderGoods;


/**
 * 品类减
 * Class CategorysDiscount
 * @package app\frontend\modules\order\discount
 */
class CategorysDiscount extends BaseDiscount
{
    protected $code = 'categorysDiscount';
    protected $name = '品类减';

    protected function _getAmount()
    {
        //对订单商品按goods_id去重 累加单品满减金额
        $result = $this->order->orderGoods->unique('goods_id')->sum(function (PreOrderGoods $orderGoods) {
            return $this->totalAmount($orderGoods);

        });

        return $result;
    }

    private function totalAmount(PreOrderGoods $orderGoods){
        // 求和所属订单中指定goods_id的订单商品支付金额
        $amount =  $this->order->orderGoods->where('goods_id', $orderGoods->goods_id)->getPaymentAmount();
        if(is_null($orderGoods->goods->hasOneSale)){
            return 0;
        }
        // order_goods的单品满减金额
        return $orderGoods->goods->hasOneSale->getEnoughReductionAmount($amount);
    }
}