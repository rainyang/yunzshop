<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/6
 * Time: 下午3:41
 */

namespace app\frontend\models;

use app\common\exceptions\AppException;
use app\common\models\PayType;
use app\common\services\PayFactory;
use app\frontend\modules\order\services\OrderService;
use app\frontend\modules\payType\BasePayType;
use app\frontend\modules\payType\CreditPay;
use app\frontend\modules\payType\Remittance;
use app\frontend\modules\payment\managers\OrderPaymentTypeManager;

class OrderPay extends \app\common\models\OrderPay
{

    public function getPaymentTypes()
    {
        /**
         * @var OrderPaymentTypeManager $orderPaymentTypeManager
         */
        $orderPaymentTypeManager = app('PaymentManager')->make('OrderPaymentTypeManager');
        $paymentTypes = $orderPaymentTypeManager->getOrderPaymentTypes($this);
        return $paymentTypes;
    }

    /**
     * 支付
     * @param int $payTypeId
     * @throws AppException
     */
    public function pay($payTypeId = null)
    {

        if (!is_null($payTypeId)) {
            $this->pay_type_id = $payTypeId;
        }
        $this->validate();

        $this->status = self::STATUS_PAID;
        $this->pay_time = time();
        $this->save();

        $this->orders->each(function ($order) {
            if (!OrderService::orderPay(['order_id' => $order->id, 'order_pay_id' => $this->id, 'pay_type_id' => $this->pay_type_id])) {
                throw new AppException('订单状态改变失败,请联系客服');
            }
        });
    }

    /**
     * 校验
     * @throws AppException
     */
    private function validate()
    {
        if (is_null($this->pay_type_id)) {
            throw new AppException('请选择支付方式');
        }
        if ($this->status > self::STATUS_UNPAID) {
            throw new AppException('(ID' . $this->id . '),此流水号已支付');
        }

        if ($this->orders->isEmpty()) {
            throw new AppException('(ID:' . $this->id . ')未找到对应订单');
        }
        $this->orders->each(function (\app\common\models\Order $order) {
            if ($order->status > Order::WAIT_PAY) {
                throw new AppException('(ID:' . $order->id . ')订单已付款,请勿重复付款');
            }
            if ($order->status == Order::CLOSE) {
                throw new AppException('(ID:' . $order->id . ')订单已关闭,无法付款');
            }
        });
    }

    public function applyPay()
    {
        return $this->getPayType()->applyPay();
    }

    /**
     * 获取支付参数
     * @param int $payTypeId
     * @param array $payParams
     * @return mixed
     * @throws AppException
     */
    public function getPayResult($payTypeId = null, $payParams = [])
    {
        if (!is_null($payTypeId)) {
            $this->pay_type_id = $payTypeId;
        }
        $this->validate();
        // 从丁哥的接口获取统一的支付参数
        $query_str = $this->getPayType()->getPayParams($payParams);
        $pay = PayFactory::create($this->pay_type_id);
        $result = $pay->doPay($query_str, $this->pay_type_id);
        if (!isset($result)) {
            throw new AppException('获取支付参数失败');
        }
        return $result;
    }

    /**
     * 获取支付类型对象
     * @return BasePayType
     */
    private function getPayType()
    {
        if (!$this->payType instanceof BasePayType) {
            if ($this->pay_type_id == PayType::CREDIT) {
                $payType = CreditPay::find($this->pay_type_id);
            } elseif ($this->pay_type_id == PayType::REMITTANCE) {
                $payType = Remittance::find($this->pay_type_id);

            } else {
                $payType = BasePayType::find($this->pay_type_id);
            }
            /**
             * @var BasePayType $payType
             */
            $payType->setOrderPay($this);
            $this->setRelation('payType', $payType);
        }
        return $this->payType;
    }

}