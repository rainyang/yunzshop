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
     * @return float|int
     * @throws \app\common\exceptions\ShopException
     */
    protected function _getAmount()
    {
        if ($this->getAmountInOrder() == null) {
            // 等到订单中此类优惠计算完成后才计算均摊的
            return null;
        }

        // (支付金额/同商品总支付金额) * 单品满减金额
        return ($this->orderGoods->getPaymentAmount() / $this->getOrderGoodsPaymentAmount()) * $this->getAmountInOrder();
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
     * 订单中同商品的总支付金额
     * @return float
     */
    protected function getOrderGoodsPaymentAmount()
    {
        return $this->orderGoods->order->orderGoods->getPaymentAmount();
    }
}