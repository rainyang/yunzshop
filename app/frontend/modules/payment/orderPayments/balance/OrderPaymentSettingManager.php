<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/27
 * Time: 下午5:20
 */

namespace app\frontend\modules\payment\orderPayments\balance;

use app\common\models\Order;


/**
 * 余额订单设置管理者
 * Class OrderPaymentSettingManager
 * @package app\frontend\modules\payment\managers
 */
class OrderPaymentSettingManager extends \app\frontend\modules\payment\OrderPaymentSettingManager
{
    public function __construct()
    {
        parent::__construct();
        $this->singleton('shop', function (OrderPaymentSettingManager $manager, Order $order) {
            return new ShopOrderPaymentSetting($order);
        });
    }
}