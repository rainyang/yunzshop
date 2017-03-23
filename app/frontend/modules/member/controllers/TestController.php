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

       $p = $pay->doRefund('2017032321001004920211490965', '1', '0.1');

       //$p = $pay->doPay('2017032321001004920211490965',2,0.1,4,5);

       redirect($p)->send();
   }

   public function add()
   {
       echo MemberService::$name;
   }
}
