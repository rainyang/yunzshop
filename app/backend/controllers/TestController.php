<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 18/04/2017
 * Time: 15:12
 */

namespace app\backend\controllers;


use app\common\components\BaseController;
use app\common\services\JsonRpc;
use app\common\services\Session;
use app\common\services\WechatPay;

class TestController extends BaseController
{
    public function index()
    {

        $result = (new JsonRpc())->client('plus',['user'=>'1','pass'=>2]);
        dd($result);
    }

    public function pay()
    {
        $pay = new WechatPay();

        $pay->doRefund('SN20170426180102628462', 1, 1);
    }
}