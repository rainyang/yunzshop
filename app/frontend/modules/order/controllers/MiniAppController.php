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
use app\common\models\MemberMiniAppModel;

class MiniAppController extends ApiController
{
    public function index()
    {
        $order = Order::find(\Yunshop::request()->orderId);
        $formId = \Yunshop::request()->formID;
        (new MessageService($order,$formId,2,'订单提交成功通知'))->received();
        return $this->successJson('成功');
    }

    public function formId(){
        $formId = \Yunshop::request()->formID;
        $memberId = \Yunshop::request()->memberId;
        $time = strtotime (date("Y-m-d H:i:s"));
        MemberMiniAppModel::where('member_id',$memberId)
            ->uniacid()
            ->update([
                'formId'=>$formId,
                'formId_create_time' =>$time,
                ]);
    }
}