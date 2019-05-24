<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/15
 * Time: 下午4:29
 */

namespace app\frontend\modules\order;

use app\framework\Database\Eloquent\Collection;
use app\frontend\modules\order\fee\BaseOrderFee;
use app\frontend\modules\order\models\PreOrder;

class OrderFee
{
    public $orderFee;
    /**
     * @var PreOrder
     */
    protected $order;

    /**
     * OrderFee constructor.
     * @param PreOrder $order
     */
    public function __construct(PreOrder $order)
    {
        $this->order = $order;

        // 订单手续费集合
        $order->setRelation('orderFees', new Collection());

    }

    public function getFee()
    {
        if (!isset($this->orderFee)) {
            $this->orderFee = collect();
            foreach (config('shop-foundation.order-fee') as $configItem) {
                $this->orderFee->put($configItem['key'], call_user_func($configItem['class'], $this->order));
            }
        }
        return $this->orderFee;
    }

    public function getAmount()
    {
        return $this->getFee()->sum(function (BaseOrderFee $orderFee) {
            // 每一种手续费
            return $orderFee->getAmount();
        });
    }


}