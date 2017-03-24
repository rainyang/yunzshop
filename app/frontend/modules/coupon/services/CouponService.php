<?php


namespace app\frontend\modules\coupon\services;

use app\backend\modules\api\services\Member;
use app\common\models\Coupon;
use app\common\models\MemberCoupon;
use app\frontend\modules\order\services\models\PreGeneratedOrderModel;
use Illuminate\Support\Facades\Log;

class CouponService
{
    const COUPON_CATEGORY_USE = 3;
    const COUPON_GOODS_USE = 4;

    public static function getMemberCoupon($used = 0) {
        return static::uniacid()->where('used', $used);
    }

    /**
     * 获得用户可使用的优惠券,预下单页
     * 传递过来一个预下单model
     * 返回可使用优惠券列表
     */
    public static function getValidCouponByPreOrder(PreGeneratedOrderModel $OrderModel)
    {
        //dd($OrderModel);
        $memberValidCouponsCollection = self::getValidCoupon($OrderModel->getMemberModel())->get();

        $memberValidCoupons = $memberValidCouponsCollection->filter(function ($memberValidCoupon) use ($OrderModel){
            return self::validUseType($memberValidCoupon, $OrderModel) &&
                self::validEnoughMoney($memberValidCoupon, $OrderModel) &&
                self::validEnoughTime($memberValidCoupon, $OrderModel);

        });

        //dd(self::calCoupon($memberValidCoupons, $OrderModel));
        //dd($memberValidCoupons);
        $data = [
            ['name' => 'sss会员等级折扣111',
                'value' => '85',
                'price' => '-50',
                'plugin' => '0',
                'coupon_id' => '1'
            ],
            ['name' => 'sss会员等级折扣111',
                'value' => '85',
                'price' => '-50',
                'plugin' => '1',
            ]
        ];
        return $data;
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

    public static function getValidCoupon($MemberModel)
    {
        return MemberCoupon::getMemberCoupon($MemberModel);
    }

    /**
     * 是否满足适用范围,如订单/通用/指定分类/指定商品
     * @param $memberValidCoupon
     * @param $OrderModel
     * @return bool
     */
    protected static function validUseType($memberValidCoupon, PreGeneratedOrderModel $OrderModel)
    {
        //dd($memberValidCoupon);
        if ($memberValidCoupon->belongsToCoupon->use_type == self::COUPON_GOODS_USE) {
            Log::info("Coupon_id:{$memberValidCoupon->coupon_id} ,指定商品不满足.");
            return false;
        }

        if ($memberValidCoupon->belongsToCoupon->use_type == self::COUPON_CATEGORY_USE) {
            Log::info("Coupon_id:{$memberValidCoupon->coupon_id} ,指定分类不满足.");
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
    protected static function validEnoughMoney($memberValidCoupon, PreGeneratedOrderModel $OrderModel)
    {
        if ($memberValidCoupon->belongsToCoupon->enough > 0 && $OrderModel->toArray()['price'] < $memberValidCoupon->belongsToCoupon->enough) {
            Log::info("Coupon_id:{$memberValidCoupon->coupon_id} ,消费金额不满足.");
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
    protected static function validEnoughTime($memberValidCoupon, PreGeneratedOrderModel $OrderModel)
    {
        //dd($memberValidCoupon->belongsToCoupon->time_limit);
        //领取后多少天可用
        if ($memberValidCoupon->belongsToCoupon->time_limit == 0
            && ceil((time() - $memberValidCoupon->get_time) / 86400) > $memberValidCoupon->belongsToCoupon->time_days) {
            //$diffDay = ceil((time() - $memberValidCoupon->get_time) / 86400);
            Log::info("Coupon_id:{$memberValidCoupon->coupon_id} ,天数条件不满足.");

            return false;
        }

        //什么时间段可用
        if ($memberValidCoupon->belongsToCoupon->time_limit == 1
            && ($memberValidCoupon->belongsToCoupon->time_start > time())
            && (time() < $memberValidCoupon->belongsToCoupon->time_end)) {
            Log::info("Coupon_id:{$memberValidCoupon->coupon_id} ,时间段条件不满足.");
            return false;
        }
        return true;
    }

    
}