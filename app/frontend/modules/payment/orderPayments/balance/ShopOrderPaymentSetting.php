<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/27
 * Time: 上午11:52
 */

namespace app\frontend\modules\payment\orderPayments\balance;

class ShopOrderPaymentSetting extends \app\frontend\modules\payment\settings\ShopOrderPaymentSetting
{
    public function canPay()
    {
        return \Setting::get('shop.pay.credit');
    }
}