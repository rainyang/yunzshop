<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/5/24
 * Time: 下午3:11
 */

namespace app\frontend\modules\order\services;

use app\backend\modules\order\models\Order;
use app\common\models\order\OrderCoupon;
use app\common\models\order\OrderDeduction;
use app\frontend\models\MemberCart;
use app\frontend\modules\orderGoods\models\PreGeneratedOrderGoods;
use app\frontend\modules\order\models\PreGeneratedOrder;
use Illuminate\Container\Container;

class OrderManager extends Container
{
    public function __construct()
    {
        $this->bindModels();
        // 订单service
        $this->singleton('OrderService', function ($orderManager) {
            return new OrderService();
        });


    }

    private function bindModels()
    {

        $this->bind('PreGeneratedOrderGoods', function ($orderManager, $attributes) {
            return new PreGeneratedOrderGoods($attributes);
        });
        $this->bind('PreGeneratedOrder', function ($orderManager, $attributes) {
            return new PreGeneratedOrder($attributes);
        });
        // 订单model
        $this->bind('Order', function ($orderManager, $attributes) {
            if (\YunShop::isApi()) {
                return new \app\frontend\models\Order($attributes);

            } else {
                return new Order();
            }
        });
        $this->bind('Member', function ($orderManager, $attributes) {
            return new \app\frontend\models\Member($attributes);
        });
        $this->bind('OrderDeduction', function ($orderManager, $attributes) {
            return new OrderDeduction($attributes);
        });
        $this->bind('OrderCoupon', function ($orderManager, $attributes) {
            return new OrderCoupon($attributes);
        });
        $this->bind('MemberCart', function ($orderManager, $attributes) {
            return new MemberCart($attributes);
        });




    }
}