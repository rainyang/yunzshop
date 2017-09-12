<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/17
 * Time: 下午12:01
 */

namespace app\common\services;

use app\common\exceptions\AppException;
use app\common\helpers\Client;
use app\common\models\Order;
use app\common\models\OrderPay;
use app\common\models\PayOrder;
use app\common\models\PayType;
use app\common\services\alipay\MobileAlipay;
use app\common\services\alipay\WebAlipay;
use app\common\services\alipay\WapAlipay;
use app\common\models\Member;
use app\common\services\alipay\AopClient;
use app\common\services\alipay\AlipayTradeRefundRequest;

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
    public function doPay($data = [], $payType = 2)
    {
        $op = "支付宝订单支付 订单号：" . $data['order_no'];
        $pay_type_name = PayType::get_pay_type_name($payType);
        $this->log($data['extra']['type'], $pay_type_name, $data['amount'], $op, $data['order_no'], Pay::ORDER_STATUS_NON, \YunShop::app()->getMemberId());

        return $this->_pay->doPay($data);
    }

    public function doRefund($out_trade_no, $totalmoney, $refundmoney='0')
    {
        $out_refund_no = $this->setUniacidNo(\YunShop::app()->uniacid);
        $op = '支付宝退款 订单号：' . $out_trade_no . '退款单号：' . $out_refund_no . '退款总金额：' . $totalmoney;
        $pay_type_id = OrderPay::get_paysn_by_pay_type_id($out_trade_no);
        $pay_type_name = PayType::get_pay_type_name($pay_type_id);

        $this->refundlog(Pay::PAY_TYPE_REFUND, $pay_type_name, $totalmoney, $op, $out_trade_no, Pay::ORDER_STATUS_NON, 0);
        
        //支付宝交易单号
        $pay_order_model = PayOrder::getPayOrderInfo($out_trade_no)->first();
        if ($pay_order_model) {
            if ($pay_type_id == 10) {
                $refund_data = array(
                    'out_trade_no' => $pay_order_model ->out_order_no,
                    'trade_no' => $pay_order_model ->trade_no,
                    'refund_amount' => $totalmoney,
                    'refund_reason' => '正常退款',
                    'out_request_no' => $out_refund_no
                );
                $result = $this->apprefund($refund_data);
                if ($result) {
                    $this->changeOrderStatus($pay_order_model, Pay::ORDER_STATUS_COMPLETE, $result['trade_no']);
                    $this->payResponseDataLog($out_trade_no, '支付宝退款', json_encode($result));
                    return true;
                } else {
                    return false;
                }
            } else {
                $alipay = app('alipay.web');
                $alipay->setOutTradeNo($pay_order_model->trade_no);
                $alipay->setTotalFee($totalmoney);

                return $alipay->refund($out_refund_no);
            }
        } else {
            return false;
        }
    }

    private function changeOrderStatus($model, $status, $trade_no)
    {
        $model->status = $status;
        $model->trade_no = $trade_no;
        $model->save();
    }

    public function apprefund($refund_data)
    {
        $aop = new AopClient();
        $request = new AlipayTradeRefundRequest();
        $aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
        $aop->appId = '2017021705717916';
        $aop->alipayrsaPublicKey = 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCnxj/9qwVfgoUh/y2W89L6BkRAFljhNhgPdyPuBV64bfQNN1PjbCzkIM6qRdKBoLPXmKKMiFYnkd6rAoprih3/PrQEB/VsW8OoM8fxn67UDYuyBTqA23MML9q1+ilIZwBC2AQ2UBVOrFXfFl75p6/B5KsiNG9zpgmLCUYuLkxpLQIDAQAB';
        $aop->rsaPrivateKey='MIICXAIBAAKBgQCxWoj9wQuXF0yZkkjpCeoU8OIHKV/S1bdmq8AJUqknyvR6qlLncU5+/pLg4v1RANqVvbvhZP6M2B0aP/Xa1osX3UN61qaEEVuhlmsJH6wyjndElBaezuD50Hon8sj9ks2Cs6BJPyTJ3zLJUHwE/GPS2rPZ5i3K+YVjCRu6m9ps/QIDAQABAoGATSOcvAowGVqH9a/byIiIaO1Q0l6bkB9msuB3GVb7vhQXfBcDEimFQ9VEW04/cfEWIdUxl9qOoQIKvnUdYT65pkEDrqYUiryWXJ3e5SsEjCvzH6a+zelDfgsHZFTBk3d1m5OFJpMqSVdQGY5kS861l5uuwrj9/VJPPXqdwOA3oQECQQDfjN74m8cg3U1YJ35nz02qCleIWsC74JamFIIjZCDYFMysXygDw/66nn/rMp/6/2+FrF2hQOI0nDg3E2MiNoo9AkEAyxj/V3rNmQlqBQ9c06JhyPD8AlhBL8XLGL7nypTBWKroOKki9BSDrTgpeSLBy7ydA40nOPcpoFzQ4x8BSTFZwQJBALIxiEqDYedAgDaUxJ3bEP1J4Rw/uwIHtA4Oqu2rEsMrUTrVXwAhaxs23KCOaheZJTxYeQngvm9RVz4PpiXPc8ECQCiHRn0YfmqpnESCOk3pO4YzwLZfEjMMT2kSv4KHiMW+5TRZXCZE6bnpWS1ZKD8V1mddBZSyjdX4b57DEyid9oECQHn6o0B++V3F0cIzg+DAVI+blKNP6C5bFcPSic9MwU8hfOA6W/QQA0wkZOR4i2xfp/ygjsygX0o/S5yWuYBaLhQ=';
        $aop->apiVersion = '1.0';
        $aop->signType = 'RSA';
        $aop->postCharset='UTF-8';
        $aop->format='json';
        $json = json_encode($refund_data);
        $request->setBizContent($json);
        $result = $aop->execute($request);
        $res = json_decode($result, 1);
        if(!empty($res)&&$res['alipay_trade_refund_response']['code'] == '10000'){
            return $res['alipay_trade_refund_response'];
        } else {
            return false;
        }
    }

    public function doWithdraw($member_id, $out_trade_no, $money, $desc = '', $type=1)
    {
        $batch_no = $this->setUniacidNo(\YunShop::app()->uniacid);

        $op = '支付宝提现 批次号：' . $out_trade_no . '提现金额：' . $money;
        $this->withdrawlog(Pay::PAY_TYPE_REFUND, $this->pay_type[Pay::PAY_MODE_ALIPAY], $money, $op, $out_trade_no, Pay::ORDER_STATUS_NON, $member_id);

        $alipay = app('alipay.web');

        $alipay->setTotalFee($money);

        $member_info = Member::getUserInfos($member_id)->first();

        if ($member_info) {
            $member_info = $member_info->toArray();
        } else {
            throw new AppException('会员不存在');
        }

        if (!empty($member_info['yz_member']['alipay']) && !empty($member_info['yz_member']['alipayname'])) {
            $account = $member_info['yz_member']['alipay'];
            $name = $member_info['yz_member']['alipayname'];
        } else {
            throw new AppException('没有设定支付宝账号');
        }

        return $alipay->withdraw($account, $name, $out_trade_no, $batch_no);
    }

    public function buildRequestSign()
    {
        // TODO: Implement buildRequestSign() method.
    }
}