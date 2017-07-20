<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/5/24
 * Time: 下午3:11
 */

namespace app\frontend\modules\order\services;

use app\backend\modules\order\models\Order;
use app\frontend\modules\orderGoods\models\PreGeneratedOrderGoods;
use app\frontend\modules\order\models\PreGeneratedOrder;
use Illuminate\Container\Container;

class OrderManager extends Container
{
    public function __construct()
    {
        //
        $this->bind('PreGeneratedOrderGoodsModel', function ($orderManager, $attributes) {
            if (1) {
                return new \app\frontend\modules\orderGoods\models\PreGeneratedOrderGoods($attributes);
            }
            return new PreGeneratedOrderGoods($attributes);
        });
        $this->bind('PreGeneratedOrderModel', function ($orderManager, $attributes) {
            if (1) {
                return new \app\frontend\modules\order\models\PreGeneratedOrder($attributes);
            }
            return new PreGeneratedOrder($attributes);
        });
        // 订单model
        $this->bind('Order', function ($orderManager) {
            if (\YunShop::isApi()) {
                return new \app\frontend\models\Order();

            } else {
                return new Order();
            }
        });
        // 订单service
        $this->singleton('OrderService', function ($orderManager) {
            return new OrderService();
        });
    }
}