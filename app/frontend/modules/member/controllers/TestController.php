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
use app\frontend\modules\member\models\Member;
use app\frontend\modules\member\services\MemberService;
use app\common\services\AliPay;


class TestController extends BaseController
{
   public function index()
   {
       $pay = new WechatPay();
       $str  = $pay->setUniacidNo(122, 5);
       echo $str . '<BR>';
       echo substr($str, 17, 5); ;
      // $pay->doWithdraw(123, time(), 0.1);
       //$result = $pay->doRefund('1490503054', '4001322001201703264702511714', 1, 1);

       $data = $pay->doPay(['order_no'=>time(),'amount'=>1, 'subject'=>'微信支付', 'body'=>'测试:2', 'extra'=>'']);

     /*  return view('order.pay', [
           'config' => $data['config'],
           'js' => $data['js']
       ])->render();*/

       exit;
       $pay = new AliPay();

      //\\ $p = $pay->doRefund('2017032421001004920213140182', '1', '0.1');

       //$p = $pay->doPay(['order_no'=>time(),'amount'=>0.2, 'subject'=>'支付宝支付', 'body'=>'测试:2', 'extra'=>'']);
       $p = $pay->doWithdraw(4,time(),'0.1','提现');
       redirect($p)->send();
   }

   public function loginApi()
   {
       $login_api = 'http://test.yunzshop.com/app/index.php?i=2&c=entry&do=shop&m=sz_yi&route=member.login.index';

       redirect($login_api)->send();
   }

   public function login()
   {
       echo 'member_id: ' . \YunShop::app()->getMemberId();
   }
}
