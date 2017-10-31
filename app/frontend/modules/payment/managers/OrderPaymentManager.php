<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/27
 * Time: 上午9:56
 */

namespace app\frontend\modules\payment\managers;

use app\common\models\Order;
use app\common\models\PayType;
use app\frontend\modules\payment\OrderPayment;
use app\frontend\modules\payment\orderPayments\balance\Balance;
use Illuminate\Container\Container;
use Illuminate\Support\Collection;

/**
 * 订单支付管理者
 * Class OrderPaymentManager
 * @package app\frontend\modules\payment\managers
 */
class OrderPaymentManager extends Container
{
    /**
     * @var PaymentManager
     */
    private $paymentManager;

    /**
     * OrderPaymentManager constructor.
     * @param PaymentManager $paymentManager
     */
    function __construct(PaymentManager $paymentManager)
    {
        $this->paymentManager = $paymentManager;
        $this->bind('balance', function (OrderPaymentManager $manager, Order $order) {
            return new Balance($order);
        });
    }

    /**
     * 获取订单可用的支付方式
     * @param $order
     * @return Collection
     */
    public function getOrderPaymentTypes($order)
    {
        /**
         * 商城中存在的支付方式集合
         * @var \Illuminate\Database\Eloquent\Collection $paymentTypes
         */
        $paymentTypes = PayType::get();
        if ($paymentTypes->isEmpty()) {
            return collect();
        }

        // 实例化订单所有支付方式
        $orderPaymentTypes = $paymentTypes->map(function(PayType $payType) use($order){
//            // 支付方式对应的支付设置
//            /**
//             * @var OrderPaymentSettingManager $orderPaymentSettingManager
//             */
//            $orderPaymentSettingManager = $this->paymentManager->make('OrderPaymentSettingManager');
//            $settings = $orderPaymentSettingManager->getOrderPaymentSettingCollection($order,$payType);

            // 对应的类在容器中注册过
            if($this->bound($payType->code)){
                return $this->make($payType->code);
            }
            return null;
        });

        // 过滤掉无效的
        $orderPaymentTypes = $orderPaymentTypes->filter(function (OrderPayment $paymentType) {
            // 可用的
            return isset($paymentType) && $paymentType->isEnable();
        });

        return $orderPaymentTypes;
    }
}