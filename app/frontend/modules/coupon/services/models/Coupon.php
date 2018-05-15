<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/28
 * Time: 下午1:48
 */

namespace app\frontend\modules\coupon\services\models;


use app\common\models\MemberCoupon;
use app\common\models\Coupon as DbCoupon;

use app\frontend\models\order\PreOrderCoupon;
use app\frontend\modules\coupon\services\MemberCouponService;
use app\frontend\modules\coupon\services\models\Price\CouponPrice;
use app\frontend\modules\coupon\services\models\Price\DiscountCouponPrice;
use app\frontend\modules\coupon\services\models\Price\MoneyOffCouponPrice;
use app\frontend\modules\coupon\services\models\TimeLimit\DateTimeRange;
use app\frontend\modules\coupon\services\models\TimeLimit\SinceReceive;
use app\frontend\modules\coupon\services\models\TimeLimit\TimeLimit;
use app\frontend\modules\coupon\services\models\UseScope\CategoryScope;
use app\frontend\modules\coupon\services\models\UseScope\CouponUseScope;
use app\frontend\modules\coupon\services\models\UseScope\GoodsScope;
use app\frontend\modules\coupon\services\models\UseScope\ShopScope;
use app\frontend\modules\order\models\PreOrder;

class Coupon
{
    /**
     * @var CouponPrice
     */
    private $price;
    /**
     * @var CouponUseScope
     */
    private $useScope;
    /**
     * @var TimeLimit
     */
    private $timeLimit;

    /**
     * @var PreOrder
     */
    private $order;
    /**
     * @var \app\common\models\MemberCoupon
     */
    private $memberCoupon;

    public function __construct(MemberCoupon $memberCoupon, PreOrder $order)
    {
        $this->memberCoupon = $memberCoupon;
        $this->order = $order;
        $this->price = $this->getPriceInstance();
        $this->useScope = $this->getUseScopeInstance();
        $this->timeLimit = $this->getTimeLimitInstance();
    }

    public function getPreOrder()
    {
        return $this->order;
    }

    public function getMemberCoupon()
    {
        return $this->memberCoupon;
    }

    /**
     * 金额类的实例
     */
    private function getPriceInstance()
    {
        switch ($this->memberCoupon->belongsToCoupon->coupon_method) {
            case DbCoupon::COUPON_MONEY_OFF:
                return new MoneyOffCouponPrice($this);
                break;
            case DbCoupon::COUPON_DISCOUNT:
                return new DiscountCouponPrice($this);
                break;
            default:
//                if (config('app.debug')) {
//                    dd($this->memberCoupon->belongsToCoupon->coupon_method);
//                    dd($this->memberCoupon);
//                    throw new AppException('优惠券优惠类型不存在');
//                }
                return null;
                break;
        }
    }

    /**
     * 使用范围类的实例
     */
    private function getUseScopeInstance()
    {
        switch ($this->memberCoupon->belongsToCoupon->use_type) {
            case DbCoupon::COUPON_GOODS_USE:
                return new GoodsScope($this);
                break;
            case DbCoupon::COUPON_CATEGORY_USE:
                return new CategoryScope($this);
                break;
            case DbCoupon::COUPON_SHOP_USE:
                return new ShopScope($this);
                break;
            default:
//                if (config('app.debug')) {
//                    dd($this->memberCoupon->belongsToCoupon->use_type);
//                    dd($this->memberCoupon->belongsToCoupon);
//                    throw new AppException('优惠券范围不存在');
//                }
                return null;

                break;
        }
    }

    /**
     * 时间限制类实例
     */
    private function getTimeLimitInstance()
    {
        switch ($this->memberCoupon->belongsToCoupon->time_limit) {
            case DbCoupon::COUPON_DATE_TIME_RANGE:
                return new DateTimeRange($this);
                break;
            case DbCoupon::COUPON_SINCE_RECEIVE:
                return new SinceReceive($this);
                break;
            default:
//                if (config('app.debug')) {
//                    dd($this->memberCoupon->belongsToCoupon);
//                    throw new AppException('时限类型不存在');
//                }

                return null;
                break;
        }
    }

    /**
     * 获取订单优惠价格
     */
    public function getDiscountAmount()
    {
        $this->setOrderGoodsDiscountPrice();

        return $this->price->getPrice();
    }

    /**
     * 激活优惠券
     */
    public function activate()
    {
        if ($this->getMemberCoupon()->selected) {
            return;
        }
        //记录优惠券被选中了
        $this->getMemberCoupon()->selected = 1;
        $this->getMemberCoupon()->used = 1;
        //dump($this->getMemberCoupon());

        // todo 订单优惠券使用记录暂时加在这里,优惠券部分需要重构
        $preOrderCoupon = new PreOrderCoupon([
            'coupon_id' => $this->memberCoupon->coupon_id,
            'member_coupon_id' => $this->memberCoupon->id,
            'name' => $this->memberCoupon->belongsToCoupon->name,
            'amount' => $this->getDiscountAmount()
        ]);
        $preOrderCoupon->setRelation('memberCoupon', $this->memberCoupon);
        $preOrderCoupon->setOrder($this->order);

    }

    /**
     * 分配优惠金额 todo 需理清与订单商品类之间的调用关系
     */
    private function setOrderGoodsDiscountPrice()
    {
        $this->price->setOrderGoodsDiscountPrice();
    }

    /**
     * 获取范围内的订单商品
     */
    public function getOrderGoodsInScope()
    {
        return $this->useScope->getOrderGoodsInScope();
    }

    /**
     * 优惠券可使用
     * @return bool
     */
    public function valid()
    {
        if (!$this->isOptional()) {

            return false;
        }
        if (!$this->unique()) {
            return false;
        }
        if (!$this->price->valid()) {

            return false;
        }
        return true;
    }

    /**
     * 用户优惠券所属的优惠券未被选中过
     * @return bool
     */
    public function unique()
    {
        $memberCoupons = MemberCouponService::getCurrentMemberCouponCache($this->getPreOrder()->belongsToMember);
        //本优惠券与某个选中的优惠券是一张 就返回false
        return !$memberCoupons->contains(function ($memberCoupon) {

            if ($memberCoupon->selected == true) {
                //本优惠券与选中的优惠券是一张
                return $memberCoupon->coupon_id == $this->getMemberCoupon()->coupon_id;
            }
            return false;
        });

    }

    /**
     * 优惠券已选中
     * @return bool
     */
    public function isChecked()
    {

        if ($this->getMemberCoupon()->selected == 1) {
            return true;
        }
        return false;
    }

    /**
     * 优惠券可选
     * @return bool
     */
    public function isOptional()
    {
        if (!isset($this->useScope)) {
            return false;
        }
        if (!isset($this->price)) {
            return false;
        }
        if (!isset($this->timeLimit)) {
            return false;
        }
        //满足范围
        if (!$this->useScope->valid()) {
            return false;
        }
        //满足额度
        if (!$this->price->isOptional()) {
            return false;
        }
        //满足时限
        if (!$this->timeLimit->valid()) {
            return false;
        }
        //未使用
        if ($this->getMemberCoupon()->used) {
            return false;
        }

        return true;
    }

    /**
     * 记录优惠券已使用
     * @return bool
     */
//    public function destroy()
//    {
//        $memberCoupon = $this->memberCoupon->fresh();
//        $memberCoupon->used = 1;
//        return $memberCoupon->save();
//    }
}