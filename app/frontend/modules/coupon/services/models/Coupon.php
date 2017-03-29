<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/28
 * Time: 下午1:48
 */

namespace app\frontend\modules\coupon\services\models;


use app\common\exceptions\AppException;
use app\common\models\MemberCoupon;
use app\common\models\Coupon as DbCoupon;

use app\frontend\modules\coupon\services\models\Price\DiscountCouponPrice;
use app\frontend\modules\coupon\services\models\Price\MoneyOffCouponPrice;
use app\frontend\modules\coupon\services\models\UseScope\GoodsScope;
use app\frontend\modules\order\services\models\PreGeneratedOrderModel;

class Coupon
{
    private $price;
    private $useScope;
    /**
     * @var PreGeneratedOrderModel
     */
    private $preGeneratedOrderModel;
    /**
     * @var \app\common\models\MemberCoupon
     */
    private $memberCoupon;

    public function __construct(MemberCoupon $memberCoupon, PreGeneratedOrderModel $preGeneratedOrderModel)
    {
        //echo 1;exit;
        $this->memberCoupon = $memberCoupon;
        $this->preGeneratedOrderModel = $preGeneratedOrderModel;
        $this->price = $this->getPriceInstance();
        $this->useScope = $this->getUseScopeInstance();
    }

    public function getPreGeneratedOrderModel()
    {
        return $this->preGeneratedOrderModel;
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
        switch ($this->memberCoupon->belongsToCoupon->back_type) {
            case DbCoupon::COUPON_MONEY_OFF:
                return new MoneyOffCouponPrice($this);
                break;
            case DbCoupon::COUPON_DISCOUNT:
                return new DiscountCouponPrice($this);
                break;
            default:
                dd($this->memberCoupon);
                throw new AppException('优惠券优惠类型不存在');
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
            default:
                dd($this->memberCoupon->belongsToCoupon);

                throw new AppException('优惠券范围不存在');
                break;
        }
    }

    /**
     * 获取订单优惠价格
     */
    public function getDiscountPrice()
    {
        return $this->price->getPrice();
    }

    public function activate()
    {
        return $this->setOrderGoodsDiscountPrice();
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
     * @return mixed
     */
    public function valid()
    {
        return $this->useScope->valid() && $this->price->valid();
    }
}