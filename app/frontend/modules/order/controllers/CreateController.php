<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/28
 * Time: 上午10:39
 */

namespace app\frontend\modules\order\controllers;

use app\common\events\order\CreatingOrder;
use app\common\exceptions\AppException;
use app\frontend\modules\member\services\MemberCartService;
use Illuminate\Support\Facades\DB;
use Request;
use app\common\events\order\AfterOrderCreatedEvent;
use app\frontend\modules\order\models\PreOrder;

class CreateController extends PreOrderController
{
    protected function getMemberCarts()
    {
        //dd(Request::query('goods'));
        $goods_params = json_decode(request()->input('goods'),true);
        return collect($goods_params)->map(function ($memberCart) {
            //dd($memberCart);exit;
            return MemberCartService::newMemberCart($memberCart);
        });
    }
    protected function validateParam(){

    }
    public function index(Request $request)
    {
        $this->validateParam();
        //订单组
        $orders = collect();
        $shopOrder = $this->getShopOrder($this->getMemberCarts());
        if($shopOrder){

            $orders->push($shopOrder);
        }
        $orders = $orders->merge($this->getPluginOrders()[0]);

        if($orders->isEmpty()){
            throw new AppException('未找到订单商品');
        }
        //生成订单,触发事件
        $order_ids = DB::transaction(function () use ($orders) {
            return $orders->map(function ($order) {
                /**
                 * @var $order PreOrder
                 */
                $order_id = $order->generate();
                event(new AfterOrderCreatedEvent($order->getOrder()));
                return $order_id;
            });
        });

        return $this->successJson('成功', ['order_ids' => $order_ids->implode(',')]);
    }
    private function getPluginOrders(){
        $event = new CreatingOrder($this->getMemberCarts());
        event($event);
        return $event->getData();
    }
}