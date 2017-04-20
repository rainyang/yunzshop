<?php
/**
 * Created by PhpStorm.
 * User: shenyang
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
use app\frontend\modules\order\services\models\PreGeneratedOrderModel;

class CreateController extends PreGeneratedController
{
    protected function getMemberCarts()
    {
        //dd(Request::query('goods'));
        $goods_params = json_decode(Request::query('goods'),true);
        return collect($goods_params)->map(function ($memberCart) {
            //dd($memberCart);exit;
            return MemberCartService::newMemberCart($memberCart);
        });
    }

    public function index(Request $request)
    {
        //dd(Request::all());
        //exit;
        $orders = collect();
        if($this->getShopOrder()){

            $orders->push($this->getShopOrder());
        }
        $orders->merge($this->getPluginOrders());
        if($orders->isEmpty()){
            throw new AppException('未找到订单商品');
        }
        $order_ids = DB::transaction(function () use ($orders) {
            return $orders->map(function ($order) {
                /**
                 * @var $order PreGeneratedOrderModel
                 */
                $order_id = $order->generate();
                event(new AfterOrderCreatedEvent($order->getOrder()));
                return $order_id;
            });
        });

        //todo 返回什么信息
        $this->successJson('成功', ['order_id' => $order_ids[0]]);
    }
    private function getPluginOrders(){
        $event = new CreatingOrder($this->getMemberCarts());
        event($event);
        return $event->getData();
    }
}