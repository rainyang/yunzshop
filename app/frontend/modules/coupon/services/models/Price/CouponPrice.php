<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/25
 * Time: 下午5:14
 */

namespace app\frontend\modules\coupon\services\models\Price;


use app\frontend\modules\coupon\services\models\Coupon;
use app\frontend\modules\orderGoods\models\PreOrderGoodsCollection;
use app\frontend\modules\order\models\PreOrder;

abstract class CouponPrice
{
    /**
     * 优惠券数据库model
     * @var
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
    abstract protected function getOrderGoodsCollectionPrice();
    abstract public function setOrderGoodsDiscountPrice();
}