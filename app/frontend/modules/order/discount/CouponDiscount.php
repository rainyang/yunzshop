<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/5/23
 * Time: 下午3:55
 */

namespace app\frontend\modules\order\discount;
use app\frontend\models\order\PreOrderDiscount;
use app\frontend\modules\coupon\models\Coupon;
use app\frontend\modules\coupon\services\CouponService;

class CouponDiscount extends BaseDiscount
{
    /**
     * @var float
     */
    private $amount;
    /**
     * 获取总金额
     * @return float
     */
    public function getAmount()
    {
        if (isset($this->amount)) {
            return $this->amount;
        }

        $this->amount = $this->_getCouponAmount();

        return $this->amount;
    }

    /**
     * 获取总金额
     * @return float
     */
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