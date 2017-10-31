<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/27
 * Time: 上午11:52
 */

namespace app\frontend\modules\payment\settings;

use app\frontend\modules\payment\OrderPaymentSetting;

abstract class ShopOrderPaymentSetting extends OrderPaymentSetting
{
    /**
     * @inheritdoc
     */
    public function isEnable()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function getWeight()
    {
        return 30;
    }
}