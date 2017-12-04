<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/11/16
 * Time: 上午9:46
 */

namespace app\frontend\modules\payment\paymentSettings\shop;


class CloudPayAliSetting extends BaseSetting
{
    public function canUse()
    {
        $set = \Setting::get('plugin.cloud_pay_set');

        return !is_null($set) && 1 == $set['switch'];
    }
    public function exist()
    {
        return  \Setting::get('plugin.cloud_pay_set') !== null;
    }
}