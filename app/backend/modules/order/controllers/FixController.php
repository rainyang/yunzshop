<?php
namespace app\backend\modules\order\controllers;

use app\common\components\BaseController;
use app\common\models\Order;
use app\common\services\TestContract;

/**
 * Created by PhpStorm.
 * User: jan
 * Date: 21/02/2017
 * Time: 11:34
 */
class FixController extends BaseController
{

    public function index()
    {
        Order::waitPay()->where('create_time',0)->update(['create_time'=>time()]);
        Order::waitSend()->where('pay_time',0)->update(['pay_time'=>time()]);
        Order::waitReceive()->where('send_time',0)->update(['send_time'=>time()]);
        Order::Completed()->where('finish_time',0)->update(['finish_time'=>time()]);
        Order::Cancelled()->where('cancel_time',0)->update(['cancel_time'=>time()]);
        echo 'ok';
    }

}