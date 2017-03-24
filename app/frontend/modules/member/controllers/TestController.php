<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/3/9
 * Time: 上午11:40
 */

namespace app\frontend\modules\member\controllers;

use app\common\components\BaseController;
use app\common\services\CreditPay;
use app\common\services\WechatPay;
use app\frontend\modules\member\services\MemberService;
use app\common\services\AliPay;


class TestController extends BaseController
{
   public function index()
   {
       //$pay = new CreditPay();
//$pay->doPay('1','2', '0.1', 4,5);

 //      exit;
       $pay = new AliPay();

       //$p = $pay->doRefund('2017032421001004920212790121', '1', '0.2');

       $p = $pay->doPay(['order_no'=>time(),'amount'=>0.01, 'subject'=>'支付宝支付', 'body'=>'测试:2', 'extra'=>'']);
       //$p = $pay->doWithdraw(4,'22220000','0.1','提现');
       redirect($p)->send();
   }

   public function add()
   {
       echo MemberService::$name;
   }
}
