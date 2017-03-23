<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2017/3/17
 * Time: 下午12:01
 */

namespace app\common\services;

use app\common\services\alipay\MobileAlipay;
use app\common\services\alipay\WebAlipay;
use app\common\services\alipay\WapAlipay;

class AliPay extends Pay
{
    private $_pay = null;

    public function __construct()
    {
        $this->_pay = $this->createFactory();

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
        if (isMobile()) {
            return 'wap';
        } elseif (is_app()) {
            return 'mobile';
        } else {
            return 'web';
        }
    }

    public function doPay($subject, $body, $amount, $order_no, $extra)
    {
        return $this->_pay->doPay($subject, $body, $amount, $order_no, $extra);
    }

    public function doRefund($out_trade_no, $out_refund_no, $totalmoney, $refundmoney='0')
    {
        $alipay = app('alipay.web');

        $alipay->setOutTradeNo($out_trade_no);
        $alipay->setTotalFee($totalmoney);

        return $alipay->refund();
    }

    public function doWithdraw($member_id, $out_trade_no, $money, $desc, $type)
    {
        // TODO: Implement doWithdraw() method.
    }

    public function buildRequestSign()
    {
        // TODO: Implement buildRequestSign() method.
    }
}