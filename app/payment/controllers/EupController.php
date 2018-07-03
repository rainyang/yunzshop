<?php

namespace app\payment\controllers;

use app\common\models\AccountWechats;
use app\payment\PaymentController;
use app\frontend\modules\finance\models\BalanceRecharge;
use app\common\services\Pay;


class EupController extends PaymentController
{
	
	private $attach = [];

    public function __construct()
    {
        parent::__construct();

        if (empty(\YunShop::app()->uniacid)) {
            $this->attach = explode('a', $_GET['OrderID']);

            \Setting::$uniqueAccountId = \YunShop::app()->uniacid = $this->attach[0];

            AccountWechats::setConfig(AccountWechats::getAccountByUniacid(\YunShop::app()->uniacid));
        }
    }

    //异步充值通知
	public function notifyUrl()
	{
		$parameter = $_GET;

        $this->log($parameter);

        if(!empty($parameter)){
            if($this->getSignResult($parameter)) {
            	$recharge_log = BalanceRecharge::ofOrderSn($this->attach[1])->withoutGlobalScope('member_id')->first();
                if ($recharge_log) {
                    \Log::debug('------EUP验证成功-----');
                    $data = [
                        'total_fee'    => floatval($parameter['Amount']),
                        'out_trade_no' => $this->attach[1],
                        'trade_no'     => 'uep',
                        'unit'         => 'yuan',
                        'pay_type'     => 'EUP支付',
                        'pay_type_id'  => 16,

                    ];
                    $this->payResutl($data);
                    \Log::debug('----EUP结束----');
                    echo 'ok';
                } else {
                    //其他错误
                }
            } else {
                //签名验证失败
            }
        }else {
            echo 'FAIL';
        }
	}

	//同步充值通知
	public function returnUrl()
    {
        $parameter = $_GET;

        if(!empty($parameter)){
            if($this->getSignResult($parameter)) {
                $recharge_log = BalanceRecharge::ofOrderSn($this->attach[1])->withoutGlobalScope('member_id')->first();
                if ($recharge_log) {
                    \Log::debug('------EUP验证成功-----');
                    $data = [
                        'total_fee'    => floatval($parameter['Amount']),
                        'out_trade_no' => $this->attach[1],
                        'trade_no'     => 'uep',
                        'unit'         => 'yuan',
                        'pay_type'     => 'EUP支付',
                        'pay_type_id'  => 16,

                    ];
                    $this->payResutl($data);
                    \Log::debug('----EUP结束----');
                    redirect(Url::absoluteApp('member/orderlist/', ['i' => \YunShop::app()->uniacid]))->send();

                } else {
                    //其他错误
                \Log::debug('----EUP充值记录不存在----');

                }
            } else {
                \Log::debug('----EUP签名验证失败----');
                //签名验证失败
            }
        }else {
            // echo 'FAIL';
            \Log::debug('----参数为空----');
        }
    }


    /**
     * 签名验证
     *
     * @return bool
     */
    public function getSignResult($parameter)
    {
    	$key = $parameter['ShopID'].$parameter['OrderID'].$parameter['Amount'].'zhijie';

    	$md5_key = md5(md5($key));

    	return $parameter['Sign'] == $md5_key;
    }

    /**
     * 支付日志
     *
     * @param $post
     */
    public function log($data)
    {
        //访问记录
        Pay::payAccessLog();
        //保存响应数据
        Pay::payResponseDataLog($this->attach[1], 'EUP充值支付', json_encode($data));
    }
}