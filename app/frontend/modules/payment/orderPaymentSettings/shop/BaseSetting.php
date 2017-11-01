<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/27
 * Time: 上午11:52
 */

namespace app\frontend\modules\payment\orderPaymentSettings\shop;

use app\common\models\Order;
use app\frontend\modules\payment\OrderPaymentSetting;

abstract class BaseSetting extends OrderPaymentSetting
{

    /**
     * ShopOrderPaymentSetting constructor.
     * @param Order $order
     */
    function __construct(Order $order)
    {
        parent::__construct($order);
    }

    /**
     * @inheritdoc
     */
    abstract public function exist();

    /**
     * @inheritdoc
     */
    public function getWeight()
    {
        return 30;
    }

    abstract public function canUse();
}