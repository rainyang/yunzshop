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
use app\common\services\AliPay;


class TestController extends BaseController
{
   public function index()
   {
       $pay = new AliPay();

       $p = $pay->doPay(1,2,3,4,5);

       echo '<pre>';print_r($p);exit;
   }

   public function add()
   {
       echo MemberService::$name;
   }
}
