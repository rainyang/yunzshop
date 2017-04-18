<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/18
 * Time: 上午10:00
 */

namespace app\backend\modules\order\controllers;

use app\common\components\BaseController;
use app\frontend\modules\order\services\OrderService;

class ChangeOrderPriceController extends BaseController
{

    public function index(\Request $request)
    {

    }

    public function store(\Request $request)
    {
        $request['order_id'] = 233;
        $request['order_goods'] = [
            [
                'id' => 1,
                'change_price' => 10,
            ], [
                'id' => 2,
                'change_price' => 20,
            ],
        ];
        $request['dispatch_price'] = 10;
        //dd(\YunShop::app()->user->name);
        list($result, $message) = OrderService::changeOrderPrice($request);
        if ($result === false) {
            return $this->errorJson($message);
        }
        return $this->successJson($message);
    }
}