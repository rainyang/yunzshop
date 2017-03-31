<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/3/9
 * Time: 上午11:40
 */

namespace app\frontend\modules\member\controllers;

use app\common\components\ApiController;
use app\common\components\BaseController;
use app\common\services\CreditPay;
use app\common\services\PayFactory;
use app\common\services\WechatPay;
use app\frontend\modules\member\models\Member;
use app\frontend\modules\member\services\MemberService;
use app\common\services\AliPay;


class TestController extends ApiController
{
   public function index()
   {

//       $pay = new WechatPay();
//       $str  = $pay->setUniacidNo(122, 5);
//       echo $str . '<BR>';
//       echo substr($str, 17, 5);
 //      $pay->doWithdraw(123, time(), 0.1);
       //$result = $pay->doRefund('1490503054', '4001322001201703264702511714', 1, 1);

//       $data = $pay->doPay(['order_no'=>time(),'amount'=>0.1, 'subject'=>'微信支付', 'body'=>'测试:2', 'extra'=>['type'=>1]]);
//
//       return view('order.pay', [
//           'config' => $data['config'],
//           'js' => $data['js']
//       ])->render();
//exit;
       $pay = new AliPay();

      //\\ $p = $pay->doRefund('2017032421001004920213140182', '1', '0.1');

       $p = $pay->doPay(['order_no'=>time(),'amount'=>0.1, 'subject'=>'支付宝支付', 'body'=>'测试:2', 'extra'=>['type'=>2]]);
       //$p = $pay->doWithdraw(4,time(),'0.1','提现');
       redirect($p)->send();
   }

   public function loginApi()
   {
       echo $_SESSION['demo'];
       exit;
       $login_api = 'http://test.yunzshop.com/app/index.php?i=2&c=entry&do=shop&m=sz_yi&route=member.login.index&type=1';

       redirect($login_api)->send();
   }

   public function login()
   {echo '<pre>';print_r($_SESSION);exit;
       $_SESSION['demo'] = 'yunzshop123';
       echo $_SESSION['demo'];
   }

   public function pay()
   {
       $pay = PayFactory::create($type);

       //微信预下单
       $data = $pay->doPay(['order_no'=>time(),'amount'=>1, 'subject'=>'微信支付', 'body'=>'测试:2', 'extra'=>'']);
       //预下单返回结果
       return view('order.pay', [
           'config' => $data['config'],
           'js' => $data['js']
       ])->render();

       //支付宝支付
       $url = $pay->doPay(['order_no'=>time(),'amount'=>1, 'subject'=>'微信支付', 'body'=>'测试:2', 'extra'=>'']);



       //订单号、退款单号、退款总金额、实际退款金额
       $result = $pay->doRefund('1490503054', '4001322001201703264702511714', 1, 1);

       //提现者用户ID、提现单号、提现金额
       $pay->doWithdraw(123, time(), 0.1);

   }
}
