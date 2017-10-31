<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/27
 * Time: 上午10:00
 */

namespace app\frontend\modules\payment;

use app\common\models\Order;
use app\frontend\modules\payment\orderPayments\OrderPaymentSettingManager;

/**
 * 支付设置
 * Class PaymentSetting
 * @package app\frontend\modules\payment
 */
abstract class OrderPayment
{
    /**
     * @var OrderPaymentSettingCollection
     */
    protected $orderPaymentSettings;

    function __construct(Order $order)
    {

        $this->order = $order;
        // todo
        //dd($this->getCode());
        /**
         * @var OrderPaymentSettingManager $settingManager
         */
        $settingManager = app('PaymentManager')->make('OrderPaymentSettingManagers')->make($this->getCode());

        $this->orderPaymentSettings = $settingManager->getOrderPaymentSettingCollection($order);

    }

    /**
     * 开启
     * @return bool
     */
    public function isEnable()
    {

        return $this->orderPaymentSettings->isEnable();
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
     * 满足使用条件
     * @return mixed
     */
    public function canPay(){
        return $this->orderPaymentSettings->canPay();
    }

    /**
     * 获取支付码
     * @return mixed
     */
    public function getCode()
    {
        // 类名小写作为默认值
        return lcfirst(end(explode('\\',static::class)));
    }
    // todo 需要密码
}