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
        $pay = PayFactory::create(PayFactory::PAY_WEACHAT);
        $data = $pay->doPay(['order_no' => $_POST['order_no'], 'amount' => $_POST['amount'], 'subject' => $_POST['subject'], 'body' => $_POST['body'], 'extra' => $_POST['extra']]);
        return $this->successJson('成功',$data);
    }

}