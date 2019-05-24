<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/15
 * Time: 下午4:29
 */

namespace app\frontend\modules\order;

use app\frontend\models\order\PreOrderDiscount;
use app\frontend\modules\order\discount\BaseDiscount;
use app\frontend\modules\order\discount\CouponDiscount;
use app\frontend\modules\order\fee\BaseOrderFee;
use app\frontend\modules\order\models\PreOrder;
use Illuminate\Support\Collection;

class OrderFee
{
    public $orderFee;
    /**
     * @var PreOrder
     */
    protected $order;

    /**
     * 优惠券类
     * @var CouponDiscount
     */

    public function __construct(PreOrder $order)
    {
        $this->order = $order;

        // 订单手续费集合
        $this->orderFee = $order->newCollection();
        $order->setRelation('orderFee', $this->orderFee);

    }

    public function getFee()
    {
        if (!isset($this->orderFee)) {
            $this->orderFee = collect();
            // todo 未开启的和金额为0的优惠项是否隐藏
            foreach (config('shop-foundation.order-discount') as $configItem) {
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