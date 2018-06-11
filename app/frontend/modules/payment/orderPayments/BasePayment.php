<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/27
 * Time: 上午10:00
 */

namespace app\frontend\modules\payment\orderPayments;

use app\common\models\PayType;
use app\frontend\models\OrderPay;
use app\frontend\modules\payment\paymentSettings\OrderPaymentSettingCollection;

/**
 * 支付设置
 * Class PaymentSetting
 * @package app\frontend\modules\payment
 */
abstract class BasePayment
{
    /**
     * @var OrderPaymentSettingCollection
     */
    protected $orderPaymentSettings;
    /**
     * @var PayType
     */
    protected $payType;
    /**
     * @var OrderPay
     */
    protected $orderPay;

    function __construct(OrderPay $orderPay, OrderPaymentSettingCollection $orderPaymentSettings)
    {

        $this->orderPay = $orderPay;

        $this->payType = $orderPay->payType;
        $this->orderPaymentSettings = $orderPaymentSettings;

    }

    /**
     * 满足使用条件
     * @return bool
     */
    public function canUse()
    {

        return $this->orderPaymentSettings->canUse();
    }

    /**
     * 获取支付方式在列表中的排序
     * @return int
     */
    public function index()
    {
        return $this->orderPaymentSettings->index();
    }

    /**
     * 获取支付码
     * @return string
     */
    public function getCode()
    {
        return $this->payType->code;
    }

    /**
     * 获取支付名
     * @return string
     */
    public function getName()
    {
        return $this->payType->name;
    }

    /**
     * 需要支付密码
     * @return bool
     */
    public function needPassword()
    {
        if (!$this->payType->need_password) {
            return false;
        }
        // 临时解决 只考虑了余额设置,后续需要改为setting中获取
        return (bool)\Setting::get('shop.pay.balance_pay_proving');
    }

    public function getId()
    {
        return $this->payType->id;
    }
}