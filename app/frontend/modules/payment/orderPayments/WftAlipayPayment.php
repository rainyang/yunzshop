<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/18
 * Time: 16:43
 */

namespace app\frontend\modules\payment\orderPayments;


class WftAlipayPayment
{
    public function canUse()
    {
        return parent::canUse() && \YunShop::plugin()->get('wft-alipay');
    }
}