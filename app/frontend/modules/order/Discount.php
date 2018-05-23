<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/15
 * Time: 下午4:29
 */

namespace app\frontend\modules\order;

use app\frontend\models\order\PreOrderDiscount;
use app\frontend\modules\order\discount\CouponDiscount;
use app\frontend\modules\order\models\PreOrder;
use Illuminate\Support\Collection;

class Discount
{
    public $orderCoupons;
    public $orderDiscounts;
    private $amount;
    /**
     * @var PreOrder
     */
    protected $order;
    private $couponDiscount;

    public function __construct(PreOrder $order)
    {
        $this->order = $order;

        // 订单优惠券使用记录集合
        $this->orderCoupons = $order->newCollection();
        $order->setRelation('orderCoupons', $this->orderCoupons);
        // 订单优惠使用记录集合
        $this->orderDiscounts = $order->newCollection();
        $order->setRelation('orderDiscounts', $this->orderDiscounts);
        $this->couponDiscount = new CouponDiscount($this->order);

    }


    /**
     * 获取订单优惠金额
     * @return float
     */

    public function getAmount()
    {
        if (!isset($this->amount)) {
            $this->amount = $this->couponDiscount->getAmount();
            $this->setOrderDiscounts();
        }
        return $this->amount;
    }

    private function setOrderDiscounts()
    {
        // 将所有订单商品的优惠
        $orderGoodsDiscounts = $this->order->orderGoods->reduce(function (Collection $result, $aOrderGoods) {
            if (isset($aOrderGoods->orderGoodsDiscounts)) {
                return $result->merge($aOrderGoods->orderGoodsDiscounts);
            }
            return $result;
        }, collect());
        // 按每个种类的优惠分组 求金额的和
        $orderGoodsDiscounts->each(function ($orderGoodsDiscount) {
            // 新类型添加
            if ($this->order->orderDiscounts->where('discount_code', $orderGoodsDiscount->discount_code)->isEmpty()) {
                $preOrderDiscount = new PreOrderDiscount([
                    'discount_code' => $orderGoodsDiscount->discount_code,
                    'amount' => $orderGoodsDiscount->amount,
                    'name' => $orderGoodsDiscount->name,

                ]);
                $preOrderDiscount->setOrder($this->order);
                return;
            }
            // 已存在的类型累加
            $this->order->orderDiscounts->where('discount_code', $orderGoodsDiscount->discount_code)->first()->amount += $orderGoodsDiscount->amount;
        });
    }


}