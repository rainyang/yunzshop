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
     * @return float|int|null
     */
    protected function _getAmount()
    {
        if(!$this->orderDiscountCalculated()){
            // 确保订单优惠先行计算
            return null;
        }
        return ($this->orderGoods->getPrice() / $this->getOrderGoodsPrice()) * $this->getAmountInOrder();
    }

    /**
     * 订单对应该商品的单品优惠
     */
    private function getAmountInOrder()
    {
        if(is_null($this->orderGoods->goods->hasOneSale)){
            return 0;
        }
        return $this->orderGoods->goods->hasOneSale->getEnoughReductionAmount($this->getOrderGoodsPrice());
    }

    /**
     * 订单中同商品的价格小计
     * @return float
     */
    protected function getOrderGoodsPrice()
    {
        return $this->orderGoods->order->orderGoods->where('goods_id', $this->orderGoods->goods_id)->getPrice();
    }
//    /**
//     * 订单中同商品的支付金额
//     * @return float
//     */
//    protected function getOrderGoodsPaymentAmount()
//    {
//        return $this->orderGoods->order->orderGoods->where('goods_id', $this->orderGoods->goods_id)->getPaymentAmount();
//    }
}