<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/3
 * Time: 上午9:10
 */

namespace app\frontend\modules\order\controllers;

use app\common\components\BaseController;
use app\common\models\Order;


class DetailController extends BaseController
{
    public function index(){
        $orderId = \Yunshop::request()->orderid;
        if ($orderId) {
            $db_order_models = Order::with(['hasManyOrderGoods'=>function($query){
                return $query->select(['id','order_id','goods_id','goods_price','total','price','title','thumb']);
            }])->get(['id','order_sn'])->first();
            $order = $db_order_models->toArray();
            return $this->successJson($data = $order);
        } else {
            return $this->errorJson($data = []);
        }
    }
}