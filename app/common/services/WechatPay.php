<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2017/3/17
 * Time: 下午12:00
 */

namespace app\common\services;

use app\backend\modules\refund\services\RefundOperationService;
use app\common\exceptions\AppException;
use app\common\helpers\Client;
use app\common\helpers\Url;
use app\common\models\McMappingFans;
use app\common\models\Member;
use app\common\models\Order;
use EasyWeChat\Foundation\Application;
use EasyWeChat\Payment\Order as easyOrder;
use app\common\services\finance\Withdraw;

class WechatPay extends Pay
{
    private $pay_type;

    public function __construct()
    {
        $this->pay_type = config('app.pay_type');
    }

    public function doPay($data = [])
    {
        $op = '微信订单支付 订单号：' . $data['order_no'];
        $pay_order_model = $this->log($data['extra']['type'], $this->pay_type[Pay::PAY_MODE_WECHAT], $data['amount'], $op, $data['order_no'], Pay::ORDER_STATUS_NON, \YunShop::app()->getMemberId());

        if (empty(\YunShop::app()->getMemberId())) {
            throw new AppException('无法获取用户ID');
        }

        $openid = Member::getOpenId(\YunShop::app()->getMemberId());
        $pay = \Setting::get('shop.pay');

        if (empty($pay['weixin_mchid']) || empty($pay['weixin_apisecret'])
            || empty($pay['weixin_appid']) || empty($pay['weixin_secret'])) {

            throw new AppException('没有设定支付参数');
        }

        $notify_url = Url::shopUrl('payment/wechat/notifyUrl.php');
        $app     = $this->getEasyWeChatApp($pay, $notify_url);
        $payment = $app->payment;
        $order = self::getEasyWeChatOrder($data, $openid, $pay_order_model);
        $result = $payment->prepare($order);
        $prepayId = null;

        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS'){
            $prepayId = $result->prepay_id;

            $this->changeOrderStatus($pay_order_model, Pay::ORDER_STATUS_WAITPAY);
        } else {
            throw new AppException($result->err_code_des);
        }

        $config = $payment->configForJSSDKPayment($prepayId);
        $config['appId'] = $pay['weixin_appid'];

        $js = $app->js->config(array('chooseWXPay'));
        $js = json_decode($js, 1);
        $js['timestamp'] = strval($js['timestamp']);

        return ['config'=>$config, 'js'=>json_encode($js)];
    }

    /**
     * 微信退款
     *
     * @param 订单号 $out_trade_no
     * @param 订单总金额 $totalmoney
     * @param 退款金额 $refundmoney
     * @return array
     */
    public function doRefund($out_trade_no, $totalmoney, $refundmoney)
    {
        $out_refund_no = $this->setUniacidNo(\YunShop::app()->uniacid);

        $op = '微信退款 订单号：' . $out_trade_no . '退款单号：' . $out_refund_no . '退款总金额：' . $totalmoney;
        $pay_order_model = $this->refundlog(Pay::PAY_TYPE_REFUND, $this->pay_type[Pay::PAY_MODE_WECHAT], $refundmoney, $op, $out_trade_no, Pay::ORDER_STATUS_NON, 0);

        $pay = \Setting::get('shop.pay');

        if (empty($pay['weixin_mchid']) || empty($pay['weixin_apisecret'])) {
            return error(1, '没有设定支付参数');
        }

        if (empty($pay['weixin_cert']) || empty($pay['weixin_key']) || empty($pay['weixin_root'])) {
            message('未上传完整的微信支付证书，请到【系统设置】->【支付方式】中上传!', '', 'error');
        }

        $notify_url = '';
        $app     = $this->getEasyWeChatApp($pay, $notify_url);
        $payment = $app->payment;

        $result = $payment->refund($out_trade_no, $out_refund_no, $totalmoney*100, $refundmoney*100);

        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS'){
            $this->changeOrderStatus($pay_order_model, Pay::ORDER_STATUS_WAITPAY, $result->transaction_id);

            $this->payResponseDataLog($out_trade_no, '微信退款', json_encode($result));


            $order_info = Order::where('uniacid',\YunShop::app()->uniacid)->where('order_sn', $result->out_trade_no)->first();
            $order_info->price = $order_info->price * 100;

            if (bccomp($order_info->price, $result->refund_fee, 2) == 0) {
                \Log::debug('订单事件触发');
                RefundOperationService::refundComplete(['order_id'=>$order_info->id]);
            }

            return true;
        } else {
            throw new AppException('退款失败');
        }
    }

    /**
     * 微信提现
     *
     * @param 提现者用户ID $member_id
     * @param 提现金额 $money
     * @param string $desc
     * @param int $type
     * @return array
     */
    public function doWithdraw($member_id, $out_trade_no, $money, $desc='', $type=1)
    {
        //$out_trade_no = $this->setUniacidNo(\YunShop::app()->uniacid);

        $op = '微信钱包提现 订单号：' . $out_trade_no . '提现金额：' . $money;
        $pay_order_model = $this->withdrawlog(Pay::PAY_TYPE_WITHDRAW, $this->pay_type[Pay::PAY_MODE_WECHAT], $money, $op, $out_trade_no, Pay::ORDER_STATUS_NON, $member_id);

        $pay = \Setting::get('shop.pay');

        if (empty($pay['weixin_mchid']) || empty($pay['weixin_apisecret'])) {
            return error(1, '没有设定支付参数');
        }

        if (empty($pay['weixin_cert']) || empty($pay['weixin_key']) || empty($pay['weixin_root'])) {
            return show_json('0', '\'未上传完整的微信支付证书，请到【系统设置】->【支付方式】中上传!\'');
        }

        $mc_mapping_fans_model = McMappingFans::getFansById($member_id);

        if ($mc_mapping_fans_model) {
            $openid = $mc_mapping_fans_model->openid;
        } else {
            return show_json('0', '提现用户不存在');
        }

        $notify_url = '';
        $app = $this->getEasyWeChatApp($pay, $notify_url);

        if ($type == 1) {//钱包
            $merchantPay = $app->merchant_pay;

            $merchantPayData = [
                'partner_trade_no' => empty($out_trade_no) ? time() . Client::random(4, true) : $out_trade_no,
                'openid' => $openid,
                'check_name' => 'NO_CHECK',
                'amount' => $money * 100,
                'desc' => empty($desc) ? '佣金提现' : $desc,
                'spbill_create_ip' => self::getClientIP(),
            ];

            //请求数据日志
            $this->payRequestDataLog($pay_order_model->id, $pay_order_model->type,
                $pay_order_model->third_type, json_encode($merchantPayData));

            $result = $merchantPay->send($merchantPayData);
        } else {//红包
            $luckyMoney = $app->lucky_money;

            $luckyMoneyData = [
                'mch_billno'       => $pay['weixin_mchid'] . date('YmdHis') . rand(1000, 9999),
                'send_name'        => \YunShop::app()->account['name'],
                're_openid'        => $openid,
                'total_num'        => 1,
                'total_amount'     => $money * 100,
                'wishing'          => empty($desc) ? '佣金提现红包' : $desc,
                'client_ip'        => self::getClientIP(),
                'act_name'         => empty($act_name) ? '佣金提现红包' : $act_name,
                'remark'           => empty($remark) ? '佣金提现红包' : $remark,
            ];

            //请求数据日志
            $this->payRequestDataLog($pay_order_model->id, $pay_order_model->type,
                $pay_order_model->third_type, json_encode($luckyMoneyData));

            $result = $luckyMoney->sendNormal($luckyMoneyData);
        }

        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS'){
            $this->changeOrderStatus($pay_order_model, Pay::ORDER_STATUS_WAITPAY);

            $this->payResponseDataLog($out_trade_no, '微信提现', json_encode($result));

            if (bccomp($pay_order_model->price, $result->amount, 2) == 0) {
                Withdraw::paySuccess($result->partner_trade_no);
            }

            return true;

        } else {
            return show_json('0', $result->err_code_des);
        }

        return false;
    }

    /**
     * 构造签名
     *
     * @var void
     */
    public function buildRequestSign()
    {
        // TODO: Implement buildRequestSign() method.
    }

    /**
     * 创建支付对象
     *
     * @param $pay
     * @return \EasyWeChat\Payment\Payment
     */
    public function getEasyWeChatApp($pay, $notify_url)
    {
        $options = [
            'app_id'  => $pay['weixin_appid'],
            'secret'  => $pay['weixin_secret'],
            // payment
            'payment' => [
                'merchant_id'        => $pay['weixin_mchid'],
                'key'                => $pay['weixin_apisecret'],
                'cert_path'          => $pay['weixin_cert'],
                'key_path'           => $pay['weixin_key'],
                'notify_url'         => $notify_url
            ]
        ];

        $app = new Application($options);

        return $app;
    }

    /**
     * 创建预下单
     *
     * @param $data
     * @param $openid
     * @param $pay_order_model
     * @return easyOrder
     */
    public static function getEasyWeChatOrder($data, $openid, &$pay_order_model)
    {
        $attributes = [
            'trade_type'       => 'JSAPI', // JSAPI，NATIVE，APP...
            'body'             => $data['subject'],
            'out_trade_no'     => $data['order_no'],
            'total_fee'        => $data['amount'] * 100, // 单位：分
            'nonce_str'        => Client::random(8) . "",
            'device_info'      => 'yun_shop',
            'attach'           => \YunShop::app()->uniacid,
            'spbill_create_ip' => self::getClientIP(),
            'openid'           => $openid
        ];

        //请求数据日志
        self::payRequestDataLog($attributes['out_trade_no'], $pay_order_model->type,
            $pay_order_model->third_type, json_encode($attributes));

        return new easyOrder($attributes);
    }

    private function changeOrderStatus($model, $status, $trade_no)
    {
        $model->status = $status;
        $model->trade_no = $trade_no;
        $model->third_type = '微信';
        $model->save();
    }
}