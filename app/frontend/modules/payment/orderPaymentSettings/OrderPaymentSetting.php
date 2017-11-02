<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/27
 * Time: 下午8:01
 */

namespace app\frontend\modules\payment\orderPaymentSettings;

use app\common\models\Order;

abstract class OrderPaymentSetting implements OrderPaymentSettingInterface
{
    /**
     * @var Order
     */
    protected $order;

    function __construct(Order $order)
    {
        $this->order = $order;
    }
}