<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/5/23
 * Time: 下午3:55
 */

namespace app\frontend\modules\orderGoods\discount;

class EnoughReduce extends BaseDiscount
{
    protected $code = 'enoughReduce';
    protected $name = '全场满减优惠';

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
        // (支付金额/订单中同种商品已计算的支付总价 ) * 全场满减金额
        // todo 这里应该使用 商品成交金额-优先级更高的N种优惠金额之和
        return ($this->orderGoods->getPrice() / $this->getOrderGoodsPrice()) * $this->getAmountInOrder();
    }

    /**
     * 订单此种优惠总金额
     * @return float
     */
    protected function getAmountInOrder()
    {
        //dump($this->code);
        return $this->orderGoods->order->getDiscount()->getAmountByCode($this->code)->getAmount();
    }

    /**
     * todo 这里应该累加 商品成交金额-优先级更高的N种优惠金额之和
     * 订单中同商品的价格小计
     * @return float
     */
    protected function getOrderGoodsPrice()
    {
        return $this->orderGoods->order->orderGoods->getPrice();
    }
}