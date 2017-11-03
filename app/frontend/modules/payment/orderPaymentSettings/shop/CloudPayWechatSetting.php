<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/11/1
 * Time: 下午10:55
 */

namespace app\frontend\modules\payment\orderPaymentSettings\shop;


class CloudPayWechatSetting extends BaseSetting
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