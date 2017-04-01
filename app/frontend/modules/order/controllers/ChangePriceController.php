<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/20
 * Time: 下午8:02
 */

namespace app\frontend\modules\order\controllers;

use app\common\components\ApiController;
use app\common\components\BaseController;
use app\frontend\modules\order\services\OrderService;

class ChangePriceController extends ApiController
{
    public function index(){
        $param = [
            'order_id' => 86,
            'order_goods' => [
                'id' => 1,
                'price' => '190',
            ],
            'dispatch_price'=>'8'

        ];
        list($result,$message) = OrderService::changeOrderPrice($param);
        if($result === false){
            return $this->errorJson($message);
        }
        return $this->successJson($message);
    }
}