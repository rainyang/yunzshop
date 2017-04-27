<?php
namespace app\frontend\modules\order\controllers;

use app\common\components\ApiController;

use app\common\events\order\AfterOrderCreatedEvent;
use app\common\events\order\ShowPreGenerateOrder;
use app\frontend\modules\goods\services\GoodsService;
use app\frontend\modules\member\services\MemberService;
use app\frontend\modules\order\models\Order;
use app\frontend\modules\order\services\OrderService;

use app\common\events\order\AfterOrderCancelPaidEvent;

/**
 * Created by PhpStorm.
 * User: jan
 * Date: 21/02/2017
 * Time: 11:34
 */
class TestController extends ApiController
{
    public function index()
    {
        OrderService::ordersPay(['order_pay_id' => 40]);

        exit;
        //dd(MemberService::getCurrentMemberModel()->defaultAddress);
        //Event::fire(new BeforeOrderCancelPaidEvent(Order::find(1)));
        /*Event::fire(new AfterOrderCancelPaidEvent(Order::find(1)));
        Event::fire(new AfterOrderCancelSentEvent(Order::find(1)));
        Event::fire(new AfterOrderPaidEvent(Order::find(1)));
        Event::fire(new AfterOrderReceivedEvent(Order::find(1)));
        Event::fire(new AfterOrderSentEvent(Order::find(1)));*/
        //$event->addMap('supplier',[1,2]);
        //$event->addMap('store',[3]);

        //controller
        //[1,2,3,4]

        //$result = $event->getData();
        //[1,2],[3]
        //$result += $this->getShopCart($result);
        //'shop'=>差集数组

    }

    private function test($a)
    {
        //echo $b;
    }



    public function testMemberModel()
    {
        $member_model = MemberService::getCurrentMemberModel();
        var_dump($member_model->uid);
        exit;
    }

    public function testGoodsModels()
    {
        $goods_models = GoodsService::getGoodsModels([1, 2]);
        var_dump($goods_models[0]->price);
        var_dump($goods_models[1]->price);
        exit;
    }

    public function testOrderGoodsModels()
    {
        $param = [
            [
                'goods_id' => 1,
                'total' => 1
            ], [
                'goods_id' => 2,
                'total' => 2
            ]
        ];
        $order_goods_models = OrderService::getOrderGoodsModels($param);


        var_dump($order_goods_models[1]->price);
        var_dump($order_goods_models[1]->total);
        var_dump($order_goods_models[0]->price);
        var_dump($order_goods_models[0]->total);


    }
}