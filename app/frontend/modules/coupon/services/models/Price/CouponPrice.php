<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/25
 * Time: 下午5:14
 */

namespace app\frontend\modules\coupon\services\models\Price;


use app\common\models\coupon\GoodsMemberCoupon;
use app\frontend\modules\coupon\services\models\Coupon;
use app\frontend\modules\orderGoods\models\PreOrderGoods;
use app\frontend\modules\orderGoods\models\PreOrderGoodsCollection;
use app\frontend\modules\order\models\PreOrder;

abstract class CouponPrice
{
    protected $isSet = false;

    /**
     * 优惠券数据库model
     * @var \app\common\models\Coupon
     */
    protected $dbCoupon;
    /**
     * @var Coupon
     */
    protected $coupon;
    /**
     * @var PreOrder
     */
    protected $orderModel;
    /**
     * @var PreOrderGoodsCollection
     */
    protected $orderGoodsModelGroup;

    public function __construct(Coupon $coupon)
    {
        $this->coupon = $coupon;
        $this->dbCoupon = $coupon->getMemberCoupon()->belongsToCoupon;
        $this->orderModel = $coupon->getPreOrder();
        //dd($this->orderModel);
    }

    /**
     * 有效的
     * @return bool
     */
    public function valid()
    {
        // 商品价格中未使用优惠的金额 不小于 满减额度
        if (!float_lesser($this->getOrderGoodsCollectionUnusedEnoughMoney(), $this->dbCoupon->enough)) {
            return true;
        }
        return false;
    }

    /**
     * 有效的
     * @return bool
     */
    public function isOptional()
    {
        // 商品价格 不小于 满减额度
        if (!float_lesser($this->getOrderGoodsCollectionPrice(), $this->dbCoupon->enough)) {
            return true;
        }
        return false;
    }

    /**
     * 累加所有商品未使用优惠的金额
     * @return mixed
     */
    protected function getOrderGoodsCollectionUnusedEnoughMoney()
    {
        $enough = $this->coupon->getOrderGoodsInScope()->sum(function ($orderGoods) {
            if (!isset($orderGoods->coupons)) {
                return 0;
            }
            return $orderGoods->coupons->sum('enough');
        });
        return $this->getOrderGoodsCollectionPrice() - $enough;
    }

    /**
     * 订单获取优惠券 金额
     * @return mixed
     */
    abstract public function getPrice();
    /**
     * 累加所有商品会员价
     * @return int
     */
    protected function getOrderGoodsCollectionPrice()
    {
        return $this->coupon->getOrderGoodsInScope()->getPrice();
    }
    /**
     * 累加所有商品支付金额
     * @return int
     */
    protected function getOrderGoodsCollectionPaymentAmount()
    {
        return $this->coupon->getOrderGoodsInScope()->getPaymentAmount();
    }
    /**
     * 分配优惠金额 立减折扣券使用 商品折扣后价格计算
     */
    public function setOrderGoodsDiscountPrice()
    {
        if($this->isSet){
            return;
        }
        $this->coupon->getOrderGoodsInScope()->map(function ($orderGoods) {
            /**
             * @var $orderGoods PreOrderGoods
             */

            $goodsMemberCoupon = new GoodsMemberCoupon();

            $goodsMemberCoupon->amount = $orderGoods->getPaymentAmount() / $this->getOrderGoodsCollectionPaymentAmount() * $this->getPrice();
            $goodsMemberCoupon->enough = $orderGoods->getPaymentAmount() / $this->getOrderGoodsCollectionPaymentAmount() * $this->dbCoupon->enough;
            //todo 需要按照订单方式修改
            if (!isset($orderGoods->coupons)) {
                $orderGoods->coupons = collect();
            }

            $orderGoods->coupons->push($goodsMemberCoupon);

        });
        $this->isSet = true;
    }
}