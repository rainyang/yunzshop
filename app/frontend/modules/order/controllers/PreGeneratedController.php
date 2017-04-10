<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/23
 * Time: 上午11:11
 */

namespace app\frontend\modules\order\controllers;

use app\common\components\ApiController;
use app\common\events\cart\GroupingCartIdEvent;
use app\common\events\discount\OnDiscountInfoDisplayEvent;
use app\common\events\dispatch\OnDispatchTypeInfoDisplayEvent;
use app\common\events\order\ShowPreGenerateOrder;
use app\common\exceptions\AppException;
use app\common\models\Order;
use app\frontend\modules\goods\services\GoodsService;
use app\frontend\modules\member\models\MemberCart;
use app\frontend\modules\member\services\MemberCartService;
use app\frontend\modules\member\services\MemberService;
use app\frontend\modules\order\services\models\PreGeneratedOrderModel;
use app\frontend\modules\order\services\OrderService;
use app\frontend\modules\shop\services\ShopService;
use Illuminate\Support\Arr;

class PreGeneratedController extends ApiController
{
    private $param;
    private $memberCarts;

    public function index()
    {

        $this->param['goods'] = [
            'goods_id' => \YunShop::request()->get('goods_id'),
            'total' => \YunShop::request()->get('total'),
            'option_id' => \YunShop::request()->get('option_id'),
        ];

        $this->memberCarts[] = MemberCartService::newMemberCart($this->param['goods']);

        $this->run();
    }

    public function cart()
    {
        if (!isset($_GET['cart_ids'])) {
            return $this->errorJson('请选择要结算的商品');
        }

        $this->run();
    }



    private function getShopOrder()
    {
        $callback = function ($memberCart) {
            /**
             * @var $memberCart MemberCart
             */
            if (empty($memberCart->goods->is_plugin)) {
                return true;
            }
            return false;
        };
        $memberCarts = OrderService::getMemberCarts($callback);
        return OrderService::createOrderByMemberCarts($memberCarts);
    }

    private function getPluginOrderData(){
        $event = new ShowPreGenerateOrder();
        event($event);
        return $event->getData();
    }
    private function run()
    {
        $order_data = collect();
        $order_data->push(OrderService::getOrderData($this->getShopOrder()));


        $order_data = $order_data->merge($this->getPluginOrderData()[0]);


        $total_price = 0;
        $total_goods_price = 0;
        $total_dispatch_price = 0;
        $data['dispatch'] = $order_data[0]['dispatch'];

        foreach ($order_data as &$order_data_item) {

            $total_price += $order_data_item['order']['price'];
            $total_goods_price += $order_data_item['order']['goods_price'];
            $total_dispatch_price += $order_data_item['order']['dispatch_price'];
            unset($order_data_item['dispatch']);
            //$order_data[] = array_merge($data, $this->getDiscountEventData($order_model));
        }
        $data += compact('total_price', 'total_dispatch_price', 'order_data', 'total_goods_price');
        return $this->successJson('成功', $data);


    }


}