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
        'time_start' => 'date',
        'time_end' =>'date',
    ];

    //前台需要整数的"立减值"
    public function getDeductAttribute($value)
    {
        return intval($value);
    }

    //前台需要整数的"折扣值", 即"打几折"
    public function getDiscountAttribute($value)
    {
        return intval($value);
    }

    //获取该用户可领取的优惠券的状态
    public static function getCouponsForMember($memberId, $memberLevel, $couponId = null, $time = null)
    {
        $res = static::uniacid()
                        ->select(['id', 'name', 'coupon_method', 'deduct', 'discount', 'enough', 'use_type', 'category_ids',
                                'categorynames', 'goods_ids', 'goods_names', 'time_limit', 'time_days', 'time_start', 'time_end', 'get_max', 'total',
                                'money', 'credit'])
                        ->where('get_type','=',1)
                        ->where('status', '=', 1)
                        ->where('get_max', '!=', 0)
                        ->where(function($query) use ($memberLevel){
                            $query->where('level_limit', '<=', $memberLevel)
                                ->orWhere(function($query){
                                    $query->whereNull('level_limit');
                                });
                        })
                        ->withCount(['hasManyMemberCoupon'])
                        ->withCount(['hasManyMemberCoupon as member_got' => function($query) use($memberId){
                            return $query->where('uid', '=', $memberId);
                        }]);

        if(!is_null($couponId)){
            $res = $res->where('id', '=', $couponId);
        }

        if(!is_null($time)){
            $res = $res->where(function($query) use ($time){
                        $query->where('time_limit', '=', 1)->where('time_end', '>', $time)
                            ->orWhere(function($query){
                                $query->where('time_limit', '=', 0)->where('time_days', '>=', 0);
                            });
                    });
        }

        return $res;
    }

    //指定ID的, 在优惠券中心可领取的, 优惠券
    public static function getAvailableCouponById($couponId)
    {
        return static::uniacid()
            ->where('id', '=', $couponId)
            ->where(function($query){
                $query->where('total', '>', 0)
                        ->orWhere(function($query){
                            $query->where('total', '=', -1);
                        });
            })
            ->where('status','=',1)
            ->where('get_type', '=', 1)
            ->first();
    }
}
