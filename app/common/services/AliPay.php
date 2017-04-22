<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2017/3/17
 * Time: 下午12:01
 */

namespace app\common\services;

use app\common\helpers\Client;
use app\common\models\PayOrder;
use app\common\services\alipay\MobileAlipay;
use app\common\services\alipay\WebAlipay;
use app\common\services\alipay\WapAlipay;
use app\common\models\Member;

class AliPay extends Pay
{
    private $_pay = null;
    private $pay_type;

    public function __construct()
    {
        $this->_pay = $this->createFactory();
        $this->pay_type = config('app.pay_type');
    }

    private function createFactory()
    {
        $type = $this->getClientType();

        switch ($type) {
            case 'web':
                $pay = new WebAlipay();
                break;
            case 'mobile':
                $pay = new MobileAlipay();
                break;
            case 'wap':
                $pay = new WapAlipay();
                break;
            default:
                $pay = null;
        }

        return $pay;
    }

    /**
     * 获取客户端类型
     *
     * @return string
     */
    private function getClientType()
    {
        if (Client::isMobile()) {
            return 'wap';
        } elseif (Client::is_app()) {
            return 'mobile';
        } else {
            return 'web';
        }
    }

    /**
     * 订单支付/充值
     *
     * @param $subject 名称
     * @param $body 详情
     * @param $amount 金额
     * @param $order_no 订单号
     * @param $extra 附加数据
     * @return string
     */
    public function doPay($data = [])
    {
        $op = "支付宝订单支付 订单号：" . $data['order_no'];

        $this->log($data['extra']['type'], $this->pay_type[Pay::PAY_MODE_ALIPAY], $data['amount'], $op, $data['order_no'], Pay::ORDER_STATUS_NON);

        return $this->_pay->doPay($data);
    }

    public function doRefund($out_trade_no, $totalmoney, $refundmoney='0')
    {
        $out_refund_no = $this->setUniacidNo(\YunShop::app()->uniacid);

        $op = '支付宝退款 订单号：' . $out_trade_no . '退款单号：' . $out_refund_no . '退款总金额：' . $totalmoney;
        $this->refundlog(Pay::PAY_TYPE_REFUND, $this->pay_type[Pay::PAY_MODE_ALIPAY], $totalmoney, $op, $out_trade_no, Pay::ORDER_STATUS_NON);

        //支付宝交易单号
        $pay_order_model = PayOrder::getPayOrderInfo($out_trade_no)->first();

        if ($pay_order_model) {
            $alipay = app('alipay.web');

            $alipay->setOutTradeNo($pay_order_model->trade_no);
            $alipay->setTotalFee($totalmoney);

            return $alipay->refund($out_refund_no);
        } else {
            return false;
        }


    }

    public function doWithdraw($member_id, $out_trade_no, $money, $desc = '', $type=1)
    {
        //$out_trade_no = $this->setUniacidNo(\YunShop::app()->uniacid);

        $op = '支付宝提现 订单号：' . $out_trade_no . '提现金额：' . $money;
        $this->log(Pay::PAY_TYPE_REFUND, $this->pay_type[Pay::PAY_MODE_ALIPAY], $money, $op, $out_trade_no, Pay::ORDER_STATUS_NON);

        $alipay = app('alipay.web');

        $alipay->setTotalFee($money);

        $member_info = Member::getUserInfos($member_id)->first()->toArray();

        if (!empty($member_info['yz-member']['alipay']) && !empty($member_info['yz-member']['alipayname'])) {
            $account = $member_info['yz-member']['alipay'];
            $name = $member_info['yz-member']['alipayname'];
        } else {
            return show_json(0);
        }

        return $alipay->withdraw($account, $name, $out_trade_no);
    }

    public function buildRequestSign()
    {
        // TODO: Implement buildRequestSign() method.
    }
}