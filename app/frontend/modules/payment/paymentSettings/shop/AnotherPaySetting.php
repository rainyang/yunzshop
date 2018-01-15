<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/1/15
 * Time: 下午3:42
 */

namespace app\frontend\modules\payment\paymentSettings\shop;


class AnotherPaySetting extends BaseSetting
{
    public function canUse()
    {
        return \Setting::get('another_pay_set');
    }

    public function exist()
    {
        return \Setting::get('another_pay_set') !== null;
    }
}