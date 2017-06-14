<?php
namespace app\frontend\modules\order\controllers;

use app\common\components\ApiController;

use app\common\events\order\AfterOrderCreatedEvent;
use app\common\events\order\AfterOrderPaidEvent;
use app\common\models\Member;
use app\common\models\Order;
use app\common\services\MessageService;
use app\frontend\modules\goods\services\GoodsService;
use app\frontend\modules\member\services\MemberService;

use app\frontend\modules\order\services\message\Message;
use app\frontend\modules\order\services\OrderService;

use Yunshop\Gold\common\services\Notice;

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 21/02/2017
 * Time: 11:34
 */
class TestController extends ApiController
{
    public function index()
    {
//$t = MessageService::getWechatTemplates();
//        dd($t);
        //\Log::info(4);
//        dd(Message::getWechatTemplates());
//        exit;
        $permissions = (new \app\frontend\modules\order\services\MessageService(\app\frontend\models\Order::first()))->paid();
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

    private function test()
    {
        //dd(\app\frontend\modules\order\services\MessageService::canceled(Order::first()));
event(new AfterOrderPaidEvent(\app\frontend\models\Order::first()));
        $openid = Member::getOpenId(213);
        if (!$openid) {
            return;
        }
        if (1) {
//            $message = $set['become_micro'];
//            $message = str_replace('[昵称]', $micro_model->hasOneMember->nickname, $message);
//            $message = str_replace('[时间]', date('Y-m-d H:i:s', time()), $message);
//            $message = str_replace('[店主等级]', $micro_model->hasOneMicroShopLevel->level_name, $message);
//            $msg = [
//                "first" => '您好',
//                "keyword1" => $set['become_micro_title'] ? $set['become_micro_title'] : "成为微店通知",
//                "keyword2" => $message,
//                "remark" => "",
//            ];
            $remark = "\n订单下单成功,请到后台查看!";

            $msg = array(
                'first' => array(
                    'value' => "订单下单通知!",
                    "color" => "#4a5077"
                ),
                'keyword1' => array(
                    'title' => '时间',
                    'value' => date('Y-m-d H:i:s'),
                    "color" => "#4a5077"
                ),
                'keyword2' => array(
                    'title' => '商品名称',
                    'value' => '商品' . '订单价格',
                    "color" => "#4a5077"
                ),
                'keyword3' => array(
                    'title' => '订单号',
                    'value' => 'sn123',
                    "color" => "#4a5077"
                ),
                'remark' => array(
                    'value' => $remark,
                    "color" => "#4a5077"
                )
            );
            MessageService::notice('9jR6KdQWpbDJUfHZrdnYoc8fHtsoTbe01Mb4KsZY4iw', $msg, $openid);
        }
        return;    }



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