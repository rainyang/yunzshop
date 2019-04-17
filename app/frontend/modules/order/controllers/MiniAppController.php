<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/28
 * Time: 上午10:39
 */

namespace app\frontend\modules\order\controllers;

use app\common\components\ApiController;
use app\frontend\modules\member\services\MemberCartService;
use app\frontend\modules\memberCart\MemberCartCollection;
use app\common\models\Order;
use app\frontend\modules\order\services\MessageService;
use app\frontend\modules\order\services\OtherMessageService;

class MiniAppController extends ApiController
{
    public function index()
    {
        $order = Order::find(\Yunshop::request()->orderId);
        $formId = \Yunshop::request()->formID;
        (new MessageService($order,$formId,2))->received();
//        (new OtherMessageService($order,$formId,2))->received();
        return $this->successJson('成功');
    }
}