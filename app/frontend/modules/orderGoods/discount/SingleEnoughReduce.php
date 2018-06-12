<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/5/23
 * Time: 下午3:55
 */

namespace app\frontend\modules\orderGoods\discount;


/**
 * 单品满减优惠
 * Class SingleEnoughReduce
 * @package app\frontend\modules\order\discount
 */
class SingleEnoughReduce extends BaseDiscount
{
    protected $code = 'singleEnoughReduce';
    protected $name = '单品满减优惠';

    /**
     * 获取金额
     * @return float|int
     * @throws \app\common\exceptions\ShopException
     */
    protected function _getAmount()
    {
        if(!$this->orderDiscountCalculated()){
            // 确保订单优惠先行计算
            return null;
        }
        return ($this->orderGoods->getPaymentAmount() / $this->getOrderGoodsPaymentAmount()) * $this->getAmountInOrder();
    }

    /**
     * 订单对应该商品的单品优惠
     */
    private function getAmountInOrder()
    {
        if(is_null($this->orderGoods->sale)){
            return 0;
        }
        return $this->orderGoods->sale->getEnoughReductionAmount($this->getOrderGoodsPaymentAmount());
    }

    /**
     * 订单中同商品的总支付金额
     * @return float
     */
    protected function getOrderGoodsPaymentAmount()
    {
        return $this->orderGoods->order->orderGoods->where('goods_id', $this->orderGoods->goods_id)->getPaymentAmount();
    }
}