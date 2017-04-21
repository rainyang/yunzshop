<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/4/20
 * Time: 下午6:05
 */

namespace app\frontend\modules\order\controllers;


use app\common\components\ApiController;
use app\frontend\modules\order\models\Order;
use app\frontend\modules\order\models\OrderGoods;

class MyCommentController extends ApiController
{
    public function index()
    {
        $list = Order::getMyCommentList(\YunShop::app()->getMemberId(), \YunShop::request()->status);
        return $this->successJson('成功', [
            'list' => $list->toArray()
        ]);
    }

    public function goods()
    {
        $list = OrderGoods::getMyCommentList(\YunShop::app()->getMemberId(), \YunShop::request()->status);
        return $this->successJson('成功', [
            'list' => $list->toArray()
        ]);
    }
}