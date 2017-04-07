<?php

namespace app\frontend\modules\coupon\services;

use app\common\models\Order;
use app\frontend\modules\coupon\services\models\Coupon;
use app\frontend\modules\coupon\services\models\DiscountCoupon;
use app\frontend\modules\coupon\services\models\MoneyOffCoupon;
use app\frontend\modules\order\services\models\PreGeneratedOrderModel;

class TestService
{
    private $order;
    private $back_type = null;

    public function __construct(PreGeneratedOrderModel $order, $back_type = null)
    {

        $this->order = $order;
        $this->back_type = $back_type;

    }

    /**
     * 获取订单优惠金额
     * @return int
     */
    public function getOrderDiscountPrice()
    {
        $result = 0;
        //统计所有优惠券的金额
        foreach ($this->getAllValidCoupons() as $coupon) {
            /**
             * @var $coupon Coupon
             */
            $coupon->activate();

            $result += $coupon->getDiscountPrice();
            //将优惠金额分配到订单商品中
        }
        return $result;
    }

    /**
     * 获取订单可算的优惠券
     * @return array
     */
    public function getOptionalCoupons()
    {
        $result = [];
        foreach ($this->getMemberCoupon() as $coupon) {
            $Coupon = new Coupon($coupon, $this->order);
            if ($Coupon->valid()) {
                $result[] = $Coupon;
            }
        }
        return $result;
    }

    public function destroyUsedCoupons()
    {
        foreach ($this->getAllValidCoupons() as $coupon){
            /**
             * @var $coupon Coupon
             */
            $coupon->destroy();
        }
    }

    /**
     * 获取所有选中并有效的优惠券
     * @return array
     */
    public function getAllValidCoupons()
    {

        $result = [];
        foreach ($this->getSelectedMemberCoupon() as $coupon) {
            $Coupon = new Coupon($coupon, $this->order);
            if ($Coupon->valid()) {
                //$Coupon->activate();
                $result[] = $Coupon;
            }
        }
        return $result;
    }

    /**
     * 用户拥有的优惠券
     * @return mixed
     */
    private function getMemberCoupon()
    {
        /*if( $this->order instanceof PreGeneratedOrderModel){
            return $this->order->getMember()->hasManyMemberCoupon($this->back_type)->get();


        }*/
        return $this->order->belongsToMember->hasManyMemberCoupon($this->back_type)->get();

    }

    /**
     * 用户拥有并选中的优惠券
     * @return array
     */
    private function getSelectedMemberCoupon()
    {
        $coupon_id = explode(',', array_get($_GET, 'coupon_ids', ''));
        $result = [];
        //dd(MemberCoupon::getMemberCoupon($this->order->getMemberModel())->get());exit;
        foreach ($this->getMemberCoupon() as $memberCoupon) {
            if (in_array($memberCoupon->coupon_id, $coupon_id)) {
                $result[] = $memberCoupon;
            }
        }
        return $result;
    }
}