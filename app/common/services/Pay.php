<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2017/3/17
 * Time: 上午9:47
 */

namespace app\common\services;

use app\common\models\PayAccessLog;
use app\common\models\PayLog;
use app\common\models\PayOrder;
use app\common\models\PayWithdrawOrder;
use app\common\models\PayRefundOrder;
use app\common\models\PayRequestDataLog;
use app\common\models\PayResponseDataLog;

abstract class Pay
{
    /**
     * 请求的参数
     *
     * @var array
     */
    protected $parameters;

    /**
     * 密钥
     *
     * @var string
     */
    protected $key;

    /**
     * 请求接口
     *
     * @var string
     */
    protected $gateUrl;

    /**
     * 统一公众号
     *
     * @var integer
     */
    protected $uniacid;

    /**
     * url请求地址
     *
     * @var string
     */
    protected $url;

    /**
     * url请求方式
     *
     * @var string
     */
    protected $method;

    /**
     * 访问IP地址
     *
     * @var string
     */
    protected $ip;

    public function __construct()
    {
        $this->init();
    }

    abstract function doPay($subject, $body, $amount, $order_no, $extra);

    abstract function doRefund();

    abstract function doWithdraw();

    abstract function buildRequestSign();

    /**
     * 获取访问URL
     *
     * @return string
     */
    private function _getHttpURL()
    {
        $url = \URL::current();
        $url .= '?' . $_SERVER['QUERY_STRING'];

        return $url;
    }

    /**
     * 获取HTTP请求方式
     *
     * @return mixed
     */
    private function _getHttpMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * 获取客户端IP
     *
     * @return string
     */
    protected function getClientIP()
    {
        return \Request::getClientIp();
    }

    /**
     * init
     *
     * @var void
     */
    protected function init()
    {
        $this->uniacid = \YunShop::app()->uniacid;
        $this->url = $this->_getHttpURL();
        $this->method = $this->_getHttpMethod();
        $this->ip = $this->getClientIP();
    }

    /**
     * 获取入口地址,不包含参数值
     *
     * @return string
     */
    protected function getGateURL() {
        return $this->gateUrl;
    }

    /**
     * 设置入口地址,不包含参数值
     *
     * @param $gateUrl
     */
    protected function setGateURL($gateUrl) {
        $this->gateUrl = $gateUrl;
    }

    /**
     * 获取参数值
     *
     * @param $parameter
     * @return mixed
     */
    protected function getParameter($parameter) {
        return $this->parameters[$parameter];
    }

    /**
     * 设置参数值
     *
     * @param $parameter
     * @param $parameterValue
     */
    protected function setParameter($parameter, $parameterValue) {
        $this->parameters[$parameter] = $parameterValue;
    }

    /**
     * 获取所有请求的参数
     *
     * @return array
     */
    protected function getAllParameters() {
        return $this->parameters;
    }

    /**
     * 获取密钥
     *
     * @return string
     */
    function getKey() {
        return $this->key;
    }

    /**
     * 设置密钥
     *
     * @param $key
     * @return void
     */
    function setKey($key) {
        $this->key = $key;
    }

    /**
     * 预下单
     *
     * @return array
     */
    protected function preOrder() {
        $params = ksort($this->parameters);
        $params = array2xml($params);

        $response = ihttp_request($this->getGateURL(), $params);

        return $response;
    }

    protected function encryption() {}

    protected function decryption() {}

    protected function noticeUrl() {}

    protected function returnUrl() {}

    /**
     * 支付访问日志
     *
     * @var void
     */
    protected function payAccessLog()
    {
        PayAccessLog::create([
            'uniacid' => $this->uniacid,
            'member_id' => \YunShop::app()->getMemberId(),
            'url' => $this->url,
            'http_method' => $this->method,
            'ip' => $this->ip
        ]);
    }

    /**
     * 支付日志
     *
     * @param $type
     * @param $third_type
     * @param $price
     * @param $operation
     */
    protected function payLog($type, $third_type, $price, $operation)
    {
        PayLog::create([
            'uniacid' => $this->uniacid,
            'member_id' => \YunShop::app()->getMemberId(),
            'type' => $type,
            'third_type' => $third_type,
            'price' => $price,
            'operation' => $operation,
            'ip' => $this->ip
        ]);
    }

    /**
     * 支付单
     *
     * @param $int_order_no
     * @param $out_order_no
     * @param $status
     * @param $type
     * @param $third_type
     * @param $price
     */
    protected function payOrder($int_order_no, $out_order_no, $status, $type, $third_type, $price)
    {
        PayOrder::create([
            'uniacid' => $this->uniacid,
            'member_id' => \YunShop::app()->getMemberId(),
            'int_order_no' => $int_order_no,
            'out_order_no' => $out_order_no,
            'status' => $status,
            'type' => $type,
            'third_type' => $third_type,
            'price' => $price,
            'ip' => $this->ip
        ]);
    }

    protected function payWithdrawOrder()
    {}

    protected function payRefundOrder()
    {}

    /**
     * 支付请求数据记录
     *
     * @param $order_id
     * @param $type
     * @param $third_type
     * @param $params
     */
    protected function payRequestDataLog($order_id, $type, $third_type, $params)
    {
        PayRequestDataLog::create([
            'uniacid' => $this->uniacid,
            'order_id' => $order_id,
            'type' => $type,
            'third_type' => $third_type,
            'params' => $params
        ]);
    }

    protected function payResponseDataLog()
    {}
}