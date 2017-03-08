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

    public function requestList($request){

        $memberId = \Yunshop::request()->memberid;
        if (!$memberId) {
            return $this->errorJson( $msg = '没有传递参数 - 用户ID', $data = []);
            exit;
        }

        $list = Order::$request()->with(['hasManyOrderGoods'=>function($query){
            return $query->select(['id','order_id','goods_id','goods_price','total','price'])
                ->with(['belongsToGood'=>function($query){
                    return $query->select(['id','price','title']);
                }]);
        }])->get(['id','status','order_sn','goods_price','price'])->toArray();

        if ($list) {
            return $this->successJson($data = $list);
        } else {
            return $this->errorJson($msg = '查询无数据', $data = []);
        }
    }


    //所有订单
    public function index(){
        // $pageSize=5;

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
        return $this->requestList('waitPay');
    }


    //待发货订单
    public function waitSend(){
        return $this->requestList('waitSend');
    }


    //待收货订单
    public function waitReceive(){
        return $this->requestList('waitReceive');
    }

    //已完成订单
    public function Completed(){
        return $this->requestList('Completed');
    }
}