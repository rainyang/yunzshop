<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/27
 * Time: 上午11:52
 */
namespace app\frontend\modules\payment\orderPaymentSettings\shop;

class AlipaySetting extends BaseSetting
{
    public function canUse()
    {
        return \Setting::get('shop.pay.alipay') && \YunShop::request()->type == 7;
    }
    public function exist()
    {
        return \Setting::get('shop.pay.alipay') !== null;
    }
}