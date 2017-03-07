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
    // $route = \Yunshop::request()->route;


    //所有订单
    public function index(){
        $pageSize=5;

        $memberId = \Yunshop::request()->memberid;
        
        if (!$memberId) {
            return $this->errorJson( $msg = '没有传递参数 - 用户ID', $data = []);
            exit;
        }

        $list = Order::with(['hasManyOrderGoods'=>function($query){
            return $query->select(['id','order_id','goods_id','goods_price','total','price'])
                            ->with(['belongsToGood'=>function($query){
                                return $query->select(['id','price','title']);
                            }]);
        }])->get(['id','status','order_sn','goods_price','price'])->toArray();
        
        if (!$list) {
            return $this->successJson($data = $list);
        } else {
            return $this->errorJson($msg = '查询无数据', $data = []);
        }
    }

    //待付款订单
    public function waitPay(){
        $pageSize=5;
        
        $list = Order::waitPay()->with(['hasManyOrderGoods'=>function($query){
            return $query->select(['id','order_id','goods_id','goods_price','total','price','title','thumb']);
        }])->get(['id','order_sn','goods_price','price'])->toArray();
        
        // dd($list);
        return $this->successJson($data = $list);
    }

    //待发货订单
    public function waitSend(){
        $pageSize=5;
        
        $list = Order::waitSend()->with(['hasManyOrderGoods'=>function($query){
            return $query->select(['id','order_id','goods_id','goods_price','total','price','title','thumb']);
        }])->get(['id','order_sn','goods_price','price'])->toArray();
        
        // dd($list);
        return $this->successJson($data = $list);
    }


    //待收货订单
    public function waitReceive(){
        $pageSize=5;
        
        $list = Order::waitReceive()->with(['hasManyOrderGoods'=>function($query){
            return $query->select(['id','order_id','goods_id','goods_price','total','price','title','thumb']);
        }])->get(['id','order_sn','goods_price','price'])->toArray();
        
        // dd($list);
        return $this->successJson($data = $list);
    }

    //已完成订单
    public function Completed(){
        $pageSize=5;
        
        $list = Order::Completed()->with(['hasManyOrderGoods'=>function($query){
            return $query->select(['id','order_id','goods_id','goods_price','total','price','title','thumb']);
        }])->get(['id','order_sn','goods_price','price'])->toArray();
        
        dd($list);
    }
}