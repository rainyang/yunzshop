<?php

namespace app\frontend\modules\coupon\services;

use app\frontend\modules\coupon\services\models\Coupon;
use app\frontend\modules\coupon\services\models\DiscountCoupon;
use app\frontend\modules\coupon\services\models\MoneyOffCoupon;
use app\frontend\modules\goods\services\models\PreGeneratedOrderGoodsModel;
use app\frontend\modules\order\services\models\PreGeneratedOrderModel;

class TestService
{
    private $_Order;
    public function __construct(PreGeneratedOrderModel $Order)
    {
        $this->_Order = $Order;

    }

    public function getOrderDiscountPrice()
    {
        //dd($this->getAllValidCoupons());
        $result = 0;
        //统计所有优惠券的金额
        foreach ($this->getAllValidCoupons() as $coupon){
            //dd($coupon);exit;
            $result += $coupon->getPrice();
        }
        return $result;
    }

    public function getOrderGoodsDiscountPrice(PreGeneratedOrderGoodsModel $OrderGoods)
    {
        //取出优惠券类,存在订单商品对象中的 优惠券优惠金额
        return $OrderGoods->coupon_discount_price;
    }

    private function getAllSelectedCoupons(){
        //url 格式 &coupon[][id]=1
        $coupon_id = array_column($_GET['coupon'],'id');
        //dd($coupon_id);exit;
        return [\app\common\models\Coupon::whereIn('id',$coupon_id)->first()];
    }

    private function getAllValidCoupons(){

        $result = [];
        foreach ($this->getAllSelectedCoupons() as $coupon){
            //todo 根据model 实例化那种优惠券(立减or折扣)
            $Coupon = new MoneyOffCoupon($this->_Order,$coupon);
            if($Coupon->valid()){
                $Coupon->activate();
                $result[] = $Coupon;
            }
        }
        return $result;
    }
}