<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/22
 * Time: 9:51
 */

namespace app\payment\controllers;

use app\payment\PaymentController;
use Illuminate\Support\Facades\DB;
use app\common\models\AccountWechats;
use app\common\services\Pay;
use Yunshop\YopPay\models\YopPayOrder;

class YoppayController extends PaymentController
{
    protected $set;

    protected  $parameters;

    public function __construct()
    {
        parent::__construct();

        $this->set = $this->getMerchantNo();

        if (empty($this->set)) {
            exit('商户不存在');
        }
        if (empty(\YunShop::app()->uniacid)) {
            \Setting::$uniqueAccountId = \YunShop::app()->uniacid = $this->set['uniacid'];
            AccountWechats::setConfig(AccountWechats::getAccountByUniacid(\YunShop::app()->uniacid));
        }
        if (!app('plugins')->isEnabled('yop-pay')) {
            echo 'Not turned on yop pay';
            exit();
        }

        $this->dealWith();
    }

    protected function getMerchantNo()
    {
        $app_key = $_REQUEST['customerIdentification'];
        $merchant_no = substr($app_key,  strrpos($app_key, 'OPR:')+4);
        $set = DB::table('yz_yop_setting')->where('merchant_no', $merchant_no)->first();

        return $set;
    }

    private function dealWith()
    {
        $yop_data = $_REQUEST['response'];

        if ($yop_data) {
            $response = \Yunshop\YopPay\common\Util\YopSignUtils::decrypt($yop_data, $this->set['son_private_key'], $this->set['yop_public_key']);
            $this->parameters = json_decode($response, true);
        }
    }

    //错误日志
    protected function yopLog($desc,$error,$data)
    {
        \Yunshop\YopPay\common\YopLog::yopLog($desc, $error,$data);
    }

    //返回日志
    protected function yopResponse($desc,$params, $type = 'unify')
    {
        \Yunshop\YopPay\common\YopLog::yopResponse($desc, $params, $type);
    }

    //异步支付通知
    public function notifyUrl()
    {
        $this->log($this->set['merchant_no'], $this->parameters);

        $this->yopResponse('支付通知', $this->parameters, 'pay');

        $this->savePayOrder();

        $data = [
            'total_fee'    => floatval($this->getParameter('orderAmount')),
            'out_trade_no' => $this->getParameter('orderId'),
            'trade_no'     => $this->getParameter('uniqueOrderNo'),
            'unit'         => 'yuan',
            'pay_type'     => '易宝支付',
            'pay_type_id'  => 25,
        ];
        $this->payResutl($data);

        echo 'SUCCESS';
        exit();
    }

    protected function savePayOrder()
    {
        $data = [
            'uniacid'=> \YunShop::app()->uniacid,
            'pay_sn' => $this->getParameter('orderId'),
            'yop_order_no' => $this->getParameter('uniqueOrderNo'),
            'order_amount' => $this->getParameter('orderAmount'),
            'total_amount' => $this->getParameter('payAmount'),
            'can_divide_amount' => $this->getParameter('payAmount'),
            'rate' =>  $this->set['rate'],
            'rate_amount'  => $this->rateAmount(),
            'pay_at' => $this->getParameter('paySuccessDate'),
        ];

        $yop_order =  new YopPayOrder();

        $yop_order->fill($data);

        $yop_order->save();
    }

    //平台分类 支付类型
    protected function platformType()
    {

        if (!empty($this->parameters['platformType'])) {
            switch ($this->parameters['platformType']) {
                case 'WECHAT': //微信
                    $status = YopPayOrder::TYPE_WECHAT;
                    break;
                case 'ALIPAY': //支付宝
                    $status = YopPayOrder::TYPE_ALIPAY;
                    break;
                case 'NET':
                    $status = YopPayOrder::TYPE_NET;
                    break;
                case 'NCPAY':
                    $status = YopPayOrder::TYPE_NCPAY;
                    break;
                case 'CFL':
                    $status = YopPayOrder::TYPE_CFL;
                    break;
                default:
                    $status = 0;
                    break;
            }
        }

        return $status;
    }

    protected function rateAmount()
    {
        $rate =  $this->set['rate'];
        $rate_amount = bcsub($this->getParameter('orderAmount'),bcmul($this->getParameter('orderAmount'), $rate, 2),2);

        return max($rate_amount, 0);
    }

    //同步通知
    public function redirectUrl()
    {

    }

    //订单超时通知地址
    public function timeoutNotifyUrl()
    {

    }

    //订单清算通知地址
    public function csUrl()
    {
        $yop_order =  YopPayOrder::csAnnal($this->getParameter('uniqueOrderNo'),  $this->getParameter('orderId'))->first();

        if (!$yop_order) {
            $this->yopLog('订单清算','易宝支付订单不存在无法清算',$this->parameters);
            exit('Record does not exist');
        }

        $data = [
            'status' => 1,
            'cs_at' => $this->getParameter('csSuccessDate'),
            'merchant_fee' => $this->getParameter('merchantFee'),
            'customer_fee' => $this->getParameter('customerFee'),
        ];

        $yop_order->fill($data);

        $yop_order->save();

        echo 'SUCCESS';
        exit();

    }

    /**
     *获取参数值
     */
    public function getParameter($parameter) {
        return isset($this->parameters[$parameter])?$this->parameters[$parameter] : '';
    }

    /**
     * 支付日志
     *
     * @param $post
     */
    public function log($out_trade_no, $data, $msg = '易宝支付')
    {
        //访问记录
        Pay::payAccessLog();
        //保存响应数据
        Pay::payResponseDataLog($out_trade_no, $msg, json_encode($data));
    }
}