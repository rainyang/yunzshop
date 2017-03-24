<?php


namespace app\frontend\modules\coupon\services;

use app\backend\modules\api\services\Member;
use app\common\models\Coupon;
use app\common\models\MemberCoupon;
use app\frontend\modules\order\services\models\PreGeneratedOrderModel;
use Illuminate\Support\Facades\Log;

class CouponService
{
    protected $OrderModel;
    protected $memberCoupon;

    public function __construct($OrderModel, $memberCoupon){
        $this->OrderModel = $OrderModel;
        $this->memberCoupon = $memberCoupon;
    }

    public function getValidCoupon()
    {
        return $this->validUseType() && $this->validEnoughMoney() && $this->validEnoughTime();
    }

    public static function calCoupon($memberValidCoupons, PreGeneratedOrderModel $OrderModel)
    {

        $couponDatas = [];
        $memberValidCoupons->each(function ($coupon) use ($OrderModel, &$couponDatas){
            //折扣
            if($coupon->belongsToCoupon->discount > 0) {
                $couponData = [
                    'name' => $coupon->belongsToCoupon->name,
                    'value' => $coupon->belongsToCoupon->discount,
                    'price' => '0',
                    'plugin' => '0',
                    'member_coupon_id' => $coupon->id
                ];
            }

            //立减
            if($coupon->belongsToCoupon->deduct > 0) {
                $couponData = [
                    'name' => $coupon->belongsToCoupon->name,
                    'value' => '0',
                    'price' => $coupon->belongsToCoupon->deduct * -1,
                    'plugin' => '0',
                    'member_coupon_id' => $coupon->id
                ];
            }
            array_push($couponDatas, $couponData);
        });

        return $couponDatas;
    }


    /**
     * 是否满足适用范围,如订单/通用/指定分类/指定商品
     * @param $memberValidCoupon
     * @param $OrderModel
     * @return bool
     */
    protected function validUseType()
    {
        //dd($this->memberCoupon);
        if ($this->memberCoupon->belongsToCoupon->use_type == Coupon::COUPON_GOODS_USE) {
            Log::info("Coupon_id:{$this->memberCoupon->coupon_id} ,指定商品不满足.");
            return false;
        }

        if ($this->memberCoupon->belongsToCoupon->use_type == Coupon::COUPON_CATEGORY_USE) {
            Log::info("Coupon_id:{$this->memberCoupon->coupon_id} ,指定分类不满足.");
            return false;
        }

        return true;
    }

    /**
     * 是否满足消费金额
     * @param $memberValidCoupon
     * @param $OrderModel
     * @return bool
     */
    protected function validEnoughMoney()
    {
        if ($this->memberCoupon->belongsToCoupon->enough > 0 && $this->OrderModel->toArray()['price'] < $this->memberCoupon->belongsToCoupon->enough) {
            Log::info("Coupon_id:{$this->memberCoupon->coupon_id} ,消费金额不满足.");
            return false;
        }

        return true;
    }

    /**
     * 优惠券是否在有效期内
     * @param $memberValidCoupon
     * @param $OrderModel
     * @return bool
     */
    protected function validEnoughTime()
    {
        //dd($memberValidCoupon->belongsToCoupon->time_limit);
        //领取后多少天可用
        if ($this->memberCoupon->belongsToCoupon->time_limit == 0
            && ceil((time() - $this->memberCoupon->get_time) / 86400) > $this->memberCoupon->belongsToCoupon->time_days) {
            //$diffDay = ceil((time() - $memberValidCoupon->get_time) / 86400);
            Log::info("Coupon_id:{$this->memberCoupon->coupon_id} ,天数条件不满足.");

            return false;
        }

        //什么时间段可用
        if ($this->memberCoupon->belongsToCoupon->time_limit == 1
            && ($this->memberCoupon->belongsToCoupon->time_start > time())
            && (time() < $this->memberCoupon->belongsToCoupon->time_end)) {
            Log::info("Coupon_id:{$this->memberCoupon->coupon_id} ,时间段条件不满足.");
            return false;
        }
        return true;
    }

    
}