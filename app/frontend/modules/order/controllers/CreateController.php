<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/28
 * Time: 上午10:39
 */

namespace app\frontend\modules\order\controllers;

use app\common\components\ApiController;
use app\common\events\order\AfterOrderCreatedEvent;
use app\common\exceptions\AppException;
use app\frontend\modules\order\services\models\PreGeneratedOrderModel;
use app\frontend\modules\order\services\OrderService;

class CreateController extends ApiController
{

    private function getPluginOrders()
    {
        $event = new getPreGenerateOrder();
        event($event);
        return $event->getData();
    }

    private function getShopOrder()
    {
        $memberCarts = OrderService::getShopMemberCarts();

        return OrderService::createOrderByMemberCarts($memberCarts);
    }

    public function index()
    {
        $orders = collect();
        $orders->push($this->getShopOrder());
        //$orders->merge($this->getPluginOrders());
        $orders->map(function ($order) {
            /**
             * @var $order PreGeneratedOrderModel
             */
            $order->generate();
            event(new AfterOrderCreatedEvent($order->getOrder()));
        });
        //todo 返回什么信息
        $this->successJson('成功', []);
    }

    private function validator($params)
    {
        if (!is_array($params)) {
            throw new AppException('请选择下单商品(非数组)');
        }
        if (!count($params)) {
            throw new AppException('请选择下单商品(空数组)');
        }
        foreach ($params as $param) {

            if (!isset($param['goods_id'])) {
                throw new AppException('请选择下单商品(缺少goods_id)');
            }
            if (!isset($param['total'])) {
                throw new AppException('请选择下单商品(缺少total)');
            }
        }
    }
}