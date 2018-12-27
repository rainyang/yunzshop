<?php


namespace app\frontend\modules\payment\paymentSettings\shop;


class DianBangScanSetting extends BaseSetting
{
    public function canUse()
    {
        $set = \Setting::get('plugin.dian_bang_scan');

        return \YunShop::request()->type != 7 && !is_null($set);
    }

    public function exist()
    {
        return \Setting::get('plugin.dian_bang_scan') !== null;
    }
}