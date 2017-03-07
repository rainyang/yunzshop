<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/3
 * Time: 上午9:10
 */

namespace app\frontend\modules\order\controllers;
use app\common\models\Order;


class DetailController
{
    public function index(){
        $db_order_models = Order::with(['hasManyOrderGoods'=>function($query){
            return $query->select(['id','order_id','goods_id','goods_price','total','price'])
                        ->with(['belongsToGood'=>function($query){
                            return $query->select(['id','title','thumb_url','price']);
                        }]);
        }])->get(['id','order_sn'])->first();
        $order = $db_order_models->toArray();
        dd($order);

        echo json_encode($db_order_models,JSON_UNESCAPED_UNICODE);
    }
}