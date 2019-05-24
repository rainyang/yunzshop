<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/5/23
 * Time: 下午3:55
 */

namespace app\frontend\modules\order\discount;

use app\common\modules\orderGoods\models\PreOrderGoods;
use app\common\models\goods\GoodsService;
/**
 * 单品满减优惠
 * Class SingleEnoughReduce
 * @package app\frontend\modules\order\discount
 */
class serviceFee extends BaseDiscount
{
    protected $code = 'serviceFee';
    protected $name = '商品服务费';

    protected function _getAmount()
    {
        //对订单商品按goods_id累加单品满减金额
        $result = 0;
         $this->order->orderGoods->each(function (PreOrderGoods $orderGoods) use(&$result) {
             $result += $this->totalAmount($orderGoods)*$orderGoods->total;
        })->sum();
        return -1*$result;
    }

    /**
     * 获取商品的服务费
     * @param PreOrderGoods $orderGoods
     * @return float
     */
    private function totalAmount(PreOrderGoods $orderGoods)
    {
        // 获取商品的服务费
        $serviceFee = (new GoodsService())->where(['goods_id' =>  $orderGoods->goods_id])->first();
        if (!$serviceFee || is_null($serviceFee->serviceFee)){
             $fee = 0;
        }else{
            $fee = $serviceFee->serviceFee;
        }
        return $fee;
    }
}