<?php

namespace app\frontend\modules\coupon\models;


class Coupon extends \app\common\models\Coupon
{
    public $table = 'yz_coupon';

    const RELATIVE_TIME_LIMIT = 0;
    const ABSOLUTE_TIME_LIMIT = 1;

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

    //获取该公众号下所有优惠券的数据
    public static function getCouponsForMember($memberId)
    {
        return static::uniacid()
                        ->select(['id', 'name', 'coupon_method', 'deduct', 'discount', 'enough', 'use_type',
                                'categorynames', 'goods_names', 'time_limit', 'time_days', 'time_start', 'time_end', 'get_max', 'total',
                                'money', 'credit'])
                        ->where('get_type','=',1)
                        ->where('status', '=', 1)
                        ->withCount(['hasManyMemberCoupon'])
                        ->withCount(['hasManyMemberCoupon as member_got' => function($query) use($memberId){
                            return $query->where('uid', '=', $memberId);
                        }]);
    }
}
