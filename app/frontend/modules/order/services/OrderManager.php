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
use app\common\models\order\OrderDiscount;
use app\common\modules\order\OrderOperationsCollector;
use app\frontend\models\MemberCart;
use app\frontend\modules\orderGoods\models\PreOrderGoods;
use app\frontend\modules\order\models\PreOrder;
use Illuminate\Container\Container;

class OrderManager extends Container
{
    private $setting;
    public function __construct()
    {
        $this->bindModels();
        // 订单service
        $this->singleton('OrderService', function ($orderManager) {
            return new OrderService();
        });
        $this->singleton(OrderOperationsCollector::class, function ($orderManager) {
            return new OrderOperationsCollector();
        });
        $this->setting = config('shop-foundation.order');
    }

    public function setting($key = null)
    {
        return array_get($this->setting,$key);
    }

    private function bindModels()
    {

        $this->bind('PreOrderGoods', function ($orderManager, $attributes) {
            return new PreOrderGoods($attributes);
        });
        $this->bind('PreOrder', function ($orderManager, $attributes) {
            return new PreOrder($attributes);
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
        $this->bind('OrderDiscount', function ($orderManager, $attributes) {
            return new OrderDiscount($attributes);
        });
        $this->bind('OrderCoupon', function ($orderManager, $attributes) {
            return new OrderCoupon($attributes);
        });
        $this->bind('MemberCart', function ($orderManager, $attributes) {
            return new MemberCart($attributes);
        });


    }
}