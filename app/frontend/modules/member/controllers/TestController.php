<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/3/9
 * Time: 上午11:40
 */

namespace app\frontend\modules\member\controllers;

use app\common\components\BaseController;
use app\frontend\modules\member\services\MemberService;
use app\common\services\WechatPay;


class TestController extends BaseController
{
   public function index()
   {
        $wx = new WechatPay();

       // $wx->doPay(1,1,1,1,1);

       echo '<pre>';print_r(\Setting::get('shop.pay'));exit;
   }

   public function add()
   {
       echo MemberService::$name;
   }
}
