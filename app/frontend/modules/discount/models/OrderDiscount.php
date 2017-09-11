<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/15
 * Time: 下午4:29
 */

namespace app\frontend\modules\discount\models;

use app\common\events\discount\OnDeductionPriceCalculatedEvent;
use app\common\models\Coupon;
use app\frontend\models\order\PreOrderCoupon;
use app\frontend\models\order\PreOrderDeduction;
use app\frontend\models\order\PreOrderDiscount;
use app\frontend\modules\coupon\services\CouponService;
use app\frontend\modules\order\models\PreGeneratedOrder;

class OrderDiscount
{
    protected $order;
    private $couponPrice;
    private $deductionPrice;
    public $orderDeductions;
    public $orderCoupons;
    public $orderDiscounts;

    public function __construct(PreGeneratedOrder $order)
    {
        $this->order = $order;
        // 订单抵扣使用记录集合
        $this->orderDeductions = $order->newCollection();
        $order->setRelation('orderDeductions', $this->orderDeductions);
        // 订单优惠券使用记录集合
        $this->orderCoupons = $order->newCollection();
        $order->setRelation('orderCoupons', $this->orderCoupons);
        // 订单优惠使用记录集合
        $this->orderDiscounts = $order->newCollection();
        $order->setRelation('orderDiscounts', $this->orderDiscounts);
    }


    /**
     * 获取订单抵扣金额
     * @return mixed
     */
    public function getDeductionPrice()
    {
        if (isset($this->deductionPrice)) {
            return $this->deductionPrice;
        }

        $this->deductionPrice = $this->_getDeductionPrice();
        // 将抵扣总金额保存在订单优惠信息表中
        $preOrderDiscount = new PreOrderDiscount([
            'discount_code' => 'deduction',
            'amount' => $this->deductionPrice,
            'name' => '抵扣金额',

        ]);
        $preOrderDiscount->setOrder($this->order);

        return $this->deductionPrice;
    }

    private function _getDeductionPrice()
    {
//        $event = new OnDeductionPriceCalculatedEvent($this->order);
//        event($event);
//        return max($this->orderDeductions->sum('amount'), 0);
        $orderDeductionInstances = app('OrderManager')->tagged('OrderDeductionInstance');
        // todo 获取到订单所有的抵扣类
        $orderDeductions = collect($orderDeductionInstances)->map(function($orderDeductionInstance){
            $orderDeduction =  new PreOrderDeduction();
            $orderDeduction->setInstance($orderDeductionInstance);
            $orderDeduction->setOrder($this->order);
            return $orderDeduction;
        });
        dd($orderDeductions->first()->toArray());
        exit;

        // 所有选中的抵扣
        return max($this->order->orderDeductions->where('isChecked',1)->sum('amount'),0);
    }

    /**
     * 获取订单优惠金额
     * @return int
     */

    public function getDiscountAmount()
    {
        return $this->getCouponAmount();
    }

    private function setOrderDiscounts()
    {
        // 将所有订单商品的优惠
        $orderGoodsDiscounts = $this->order->orderGoods->reduce(function ($result, $aOrderGoods) {
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
            $this->order->orderDiscounts->where('discount_code', $orderGoodsDiscount->discount_code)->first()->amount += 10000;
        });
    }

    /**
     * 获取订单优惠券总金额
     * @return int
     */
    public function getCouponAmount()
    {
        if (isset($this->couponPrice)) {
            return $this->couponPrice;
        }
        $this->setOrderDiscounts();

        $this->couponPrice = $this->_getCouponAmount();

        return $this->couponPrice;
    }

    private function _getCouponAmount()
    {
        // 优先计算折扣类订单优惠券
        $discountCouponService = (new CouponService($this->order, Coupon::COUPON_DISCOUNT));
        $discountPrice = $discountCouponService->getOrderDiscountPrice();
        $discountCouponService->activate();
        //dd($discountPrice);

        // 满减订单优惠券
        $moneyOffCouponService = (new CouponService($this->order, Coupon::COUPON_MONEY_OFF));
        $moneyOffPrice = $moneyOffCouponService->getOrderDiscountPrice();
        //dd($moneyOffPrice);
        $moneyOffCouponService->activate();

        $result = $discountPrice + $moneyOffPrice;
        // 将抵扣总金额保存在订单优惠信息表中
        $preOrderDiscount = new PreOrderDiscount([
            'discount_code' => 'coupon',
            'amount' => $result,
            'name' => '优惠券总金额',

        ]);
        $preOrderDiscount->setOrder($this->order);
        return $result;
    }

}