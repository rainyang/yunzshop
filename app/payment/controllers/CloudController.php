<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/8/4
 * Time: 下午4:04
 */

namespace app\payment\controllers;


use app\common\helpers\Url;
use app\common\models\AccountWechats;
use app\payment\PaymentController;
use Yunshop\CloudPay\services\CloudPayNotifyService;

class CloudController extends PaymentController
{
    private $attach = [];

    public function __construct()
    {
        parent::__construct();

        if (empty(\YunShop::app()->uniacid)) {
            $this->attach = explode(':', $_GET['attach']);
            \Log::debug('------回调-----', $_GET);
            \Setting::$uniqueAccountId = \YunShop::app()->uniacid = $this->attach[0];

            AccountWechats::setConfig(AccountWechats::getAccountByUniacid(\YunShop::app()->uniacid));
        }
    }

    public function notifyUrl()
    {
        //$this->log($_GET);
        $this->getSignResult();
        if ('00' == $_GET['respcd'] && $_GET['errorDetail'] == "SUCCESS") {
            \Log::debug('------验证成功-----');
            $data = [
                'total_fee'    => floatval($_GET['txamt']),
                'out_trade_no' => $_GET['orderNum'],
                'trade_no'     => $_GET['channelOrderNum'],
                'unit'         => 'fen',
                'pay_type'     => '云微信支付'
            ];

            $this->payResutl($data);
            echo "success";
        } else {
            echo "fail";
        }
    }

    public function returnUrl()
    {
        if (0 == $_GET['state'] && $_GET['errorDetail'] == '成功') {
            redirect(Url::absoluteApp('member/payYes', ['i' => $_GET['attach']]))->send();
        } else {
            redirect(Url::absoluteApp('member/payErr', ['i' => $_GET['attach']]))->send();
        }
    }

    /**
     * 签名验证
     *
     * @return bool
     */
    public function getSignResult()
    {
        $pay = \Setting::get('plugin.cloud_pay_set');

        $notify = new CloudPayNotifyService();
        $notify->setKey($pay['key']);

        return $notify->verifySign();
    }
}