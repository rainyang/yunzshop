<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/27
 * Time: 上午10:00
 */

namespace app\frontend\modules\payment;

use app\common\models\Order;
use app\frontend\modules\payment\managers\OrderPaymentSettingManager;

/**
 * 支付设置
 * Class PaymentSetting
 * @package app\frontend\modules\payment
 */
class OrderPayment
{
    /**
     * @var OrderPaymentSettingCollection
     */
    protected $orderPaymentSettings;
    /**
     * @var string
     */
    protected $code;

    function __construct($code,Order $order,OrderPaymentSettingCollection $orderPaymentSettings)
    {
        $this->order = $order;
        $this->code = $code;
        $this->orderPaymentSettings = $orderPaymentSettings;

    }

    /**
     * 开启
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
     * 满足使用条件
     * @return mixed
     */
    public function canPay()
    {
        return $this->orderPaymentSettings->canPay();
    }

    /**
     * 获取支付码
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }
    // todo 需要密码
}