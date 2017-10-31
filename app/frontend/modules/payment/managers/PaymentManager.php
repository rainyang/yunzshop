<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/27
 * Time: 上午10:17
 */

namespace app\frontend\modules\payment\managers;

use Illuminate\Container\Container;

/**
 * 支付管理者
 * Class PaymentManager
 * @package app\frontend\modules\payment\managers
 */
class PaymentManager extends Container
{
    function __construct()
    {
        $this->singleton('OrderPaymentManager',function(PaymentManager $manager){
            return new OrderPaymentManager($manager);
        });
        $this->singleton('OrderPaymentSettingManagers',function(OrderPaymentManager $manager){
            return new OrderPaymentSettingManagers();
        });
    }
}