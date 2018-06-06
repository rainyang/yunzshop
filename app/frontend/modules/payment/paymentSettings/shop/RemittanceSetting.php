<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/27
 * Time: 上午11:52
 */

namespace app\frontend\modules\payment\paymentSettings\shop;

class RemittanceSetting extends BaseSetting
{
    public function canUse()
    {
        return 1;
        return \Setting::get('shop.pay.remittance');
    }

    public function exist()
    {
        return 1;

        return \Setting::get('shop.pay.remittance') !== null;
    }
}