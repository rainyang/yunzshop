<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/1
 * Time: 下午5:11
 */

namespace app\frontend\modules\order\controllers;
use app\common\components\BaseController;

use app\common\helpers\PaginationHelper;
use app\common\models\Order;

class ListController extends BaseController
{
    //所有订单的列表
    public function index(){
        $pageSize=5;
        
        $list = Order::with(['hasManyOrderGoods'=>function($query){
            return $query->select(['id','order_id','goods_id','goods_price','total','price'])
                            ->with(['belongsToGood'=>function($query1){
                                return $query1->select(['id','price','title']);
                            }]);
        }])->get(['id','order_sn','goods_price','price'])->toArray();
        
        dd($list);

    }

    //待付款订单列表
    public function waitPay(){
        // $db_order_models = Order::waitPay()->with('hasManyOrderGoods')->get();
        // //dd($db_order_models);
        // $order_models = $db_order_models;
        // dd($order_models[0]->button_models);
        // exit;
        $list = Order::waitPay()->with(['hasManyOrderGoods'=>function($query){
            return $query->select(['id','order_id','goods_id','goods_price','total','price'])
                            ->with(['belongsToGood'=>function($query1){
                                return $query1->select(['id','price','title']);
                            }]);
        }])->get(['id','order_sn','goods_price','price'])->toArray();
        
        dd($list);
    }

    //
}