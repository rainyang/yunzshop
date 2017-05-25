<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/20
 * Time: 下午6:05
 */

namespace app\frontend\modules\order\controllers;


use app\common\components\ApiController;
use app\frontend\models\Order;
use app\frontend\models\OrderGoods;

class MyCommentController extends ApiController
{
    public function index()
    {
        $list = Order::getMyCommentList( \YunShop::request()->status);
        return $this->successJson('成功', [
            'list' => $list->toArray()
        ]);
    }

    public function goods()
    {
        $list = OrderGoods::getMyCommentList(1);
        return $this->successJson('成功', [
            'list' => $list->toArray()
        ]);
    }
}