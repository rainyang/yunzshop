<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/5/23
 * Time: 下午3:55
 */

namespace app\frontend\modules\order\discount;

class SingleEnoughReduce extends BaseDiscount
{
    protected $code = 'enoughReduce';
    protected $name = '全场满减优惠';
    /**
     * 获取总金额
     * @return float
     */
    protected function _getAmount()
    {
        $result = 0;
        //对订单商品去重 累加getPaymentAmount
        return $result;
    }
}