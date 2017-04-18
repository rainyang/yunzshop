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
        //dd(\YunShop::app()->user->name);
        list($result,$message) = OrderService::changeOrderPrice($this->param);
        if($result === false){
            return $this->errorJson($message);
        }
        return $this->successJson($message);
    }
}