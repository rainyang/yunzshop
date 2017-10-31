<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/27
 * Time: 下午5:20
 */

namespace app\frontend\modules\payment\managers;

use Illuminate\Container\Container;

/**
 * 余额订单设置管理者
 * Class OrderPaymentSettingManager
 * @package app\frontend\modules\payment\managers
 */
class OrderPaymentSettingManagers extends Container
{
    public function __construct()
    {
        $this->singleton('balance', function (OrderPaymentSettingManagers $manager) {
            return new \app\frontend\modules\payment\orderPayments\balance\OrderPaymentSettingManager();
        });
    }
}