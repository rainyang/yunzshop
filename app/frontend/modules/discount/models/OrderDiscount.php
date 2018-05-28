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
use app\common\models\order\OrderDeduction;
use app\frontend\models\order\PreOrderCoupon;
use app\frontend\models\order\PreOrderDeduction;
use app\frontend\models\order\PreOrderDiscount;
use app\frontend\modules\deduction\models\Deduction;
use app\frontend\modules\coupon\services\CouponService;
use app\frontend\modules\order\models\PreOrder;
use Illuminate\Database\Eloquent\Collection;

class OrderDiscount
{
    public $orderDeductions;
    public $orderCoupons;
    public $orderDiscounts;
    protected $order;
    private $couponPrice;
    private $deductionPrice;

    public function __construct(PreOrder $order)
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
        if($this->deductionPrice){
            // 将抵扣总金额保存在订单优惠信息表中
            $preOrderDiscount = new PreOrderDiscount([
                'discount_code' => 'deduction',
                'amount' => $this->deductionPrice,
                'name' => '抵扣金额',

            ]);
            $preOrderDiscount->setOrder($this->order);
        }

        return $this->deductionPrice;
    }

    private function _getDeductionPrice()
    {
        /**
         * 商城开启的抵扣
         * @var Collection $deductions
         */
        $deductions = Deduction::whereEnable(1)->get();

        if($deductions->isEmpty()){
            return 0;
        }
        // 过滤调无效的
        $deductions = $deductions->filter(function($deduction){
            /**
             * @var Deduction $deduction
             */
            return $deduction->valid();
        });
        // todo 按照用户勾选顺序排序
        $sort = array_flip($this->order->getParams('deduction_ids'));
        $deductions = $deductions->sortBy(function($deduction) use($sort){
            return array_get($sort,$deduction->code,999);
        });

        // 遍历抵扣集合, 实例化订单抵扣类 ,向其传入订单模型和抵扣模型 返回订单抵扣集合
        $orderDeductions = $deductions->map(function($deduction){

            $orderDeduction = new PreOrderDeduction([],$deduction,$this->order);

            return $orderDeduction;
        });

        // 求和订单抵扣集合中所有已选中的可用金额
        $result = $orderDeductions->sum(function($orderDeduction){
            /**
             * @var PreOrderDeduction $orderDeduction
             */
            if($orderDeduction->isChecked()){
                return $orderDeduction->getUsablePoint()->getMoney();
            }
            return 0;
        });

        // 返回 订单抵扣金额
        return $result;
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
            $this->order->orderDiscounts->where('discount_code', $orderGoodsDiscount->discount_code)->first()->amount += $orderGoodsDiscount->amount;
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