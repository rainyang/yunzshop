<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/3/9
 * Time: 上午9:38
 */

namespace app\frontend\modules\order\controllers;

use app\common\components\BaseController;
use app\common\facades\Setting;
use app\common\models\Order;
use app\common\services\PayFactory;
use app\frontend\modules\order\services\VerifyPayService;

class TestPayController extends BaseController
{

    public function index()
    {
        $pay = new WechatPay();
//       $str  = $pay->setUniacidNo(122, 5);
//       echo $str . '<BR>';
//       echo substr($str, 17, 5);
        // $pay->doWithdraw(123, time(), 0.1);
        //$result = $pay->doRefund('1490503054', '4001322001201703264702511714', 1, 1);

        $data = $pay->doPay(['order_no'=>time(),'amount'=>0.1, 'subject'=>'微信支付', 'body'=>'测试:2', 'extra'=>'']);

        return view('order.pay', [
            'config' => $data['config'],
            'js' => $data['js']
        ])->render();

        exit;
    }
    public function test(){
        define('IS_TEST',true);
        $param = [
            'order_no' => time(),
            'amount' => 0.1,
            'subject' => '微信支付',
            'body' => '商品的描述:2',
            'extra' => ''
        ];
        $pay = PayFactory::create(PayFactory::PAY_WEACHAT);
        $data = $pay->doPay($param);
        dump($data);exit;
    }
}