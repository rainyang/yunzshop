<?php
/**
 * Created by PhpStorm.
 * User: win 10
 * Date: 2019/8/8
 * Time: 13:33
 */

namespace app\frontend\modules\payment\paymentSettings\shop;


class DepositSetting extends BaseSetting
{
    public function canUse()
    {

        return true;
    }

    public function exist()
    {

        return true;
    }
}