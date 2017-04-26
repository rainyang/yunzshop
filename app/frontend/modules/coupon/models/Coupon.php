<?php

namespace app\frontend\modules\coupon\models;


class Coupon extends \app\common\models\Coupon
{
    public $table = 'yz_coupon';

    protected $casts = [
        'goods_ids' => 'json',
        'category_ids' => 'json',
        'goods_names' => 'json',
        'categorynames' => 'json',
        'time_start' => 'datetime',
        'time_end' =>'datetime',
    ];

    //前台需要整数的"立减值"
    public function getDeductAttribute($value)
    {
        return intval($value);
    }

    //前台需要整数的"折扣值", 即"打几折"
    public function getDiscountAttribute($value)
    {
        return $value * 10;
    }

    //获取该用户可领取的优惠券的状态
    public static function getCouponsForMember($memberId, $memberLevel, $couponId = null)
    {
        $res = static::uniacid()
                        ->select(['id', 'name', 'coupon_method', 'deduct', 'discount', 'enough', 'use_type',
                                'categorynames', 'goods_ids', 'goods_names', 'time_limit', 'time_days', 'time_start', 'time_end', 'get_max', 'total',
                                'money', 'credit'])
                        ->where('level_limit', '>=', $memberLevel)
                        ->orWhere('level_limit', '=', -1)
                        ->where('get_type','=',1)
                        ->where('status', '=', 1)
                        ->withCount(['hasManyMemberCoupon'])
                        ->withCount(['hasManyMemberCoupon as member_got' => function($query) use($memberId){
                            return $query->where('uid', '=', $memberId);
                        }]);
        if(!is_null($couponId)){
            $res = $res->where('id', '=', $couponId);
        }
        return $res;
    }

    //指定ID的, 在优惠券中心可领取的, 优惠券
    public static function getAvailableCouponById($couponId)
    {
        return static::getCouponById($couponId)
            ->where('total', '>', 0)
            ->orwhere('total', '=', -1)
            ->where('status','=',1)
            ->where('get_type', '=', 1)
            ->first();
    }
}
