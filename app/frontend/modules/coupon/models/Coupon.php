<?php

namespace app\frontend\modules\coupon\models;

/**
 * Class Coupon
 * @package app\frontend\modules\coupon\models
 * @property int status
 * @property int get_type
 * @property int level_limit
 */
class Coupon extends \app\common\models\Coupon
{
    public $table = 'yz_coupon';

    protected $casts = [
        'goods_ids' => 'json',
        'category_ids' => 'json',
        'goods_names' => 'json',
        'categorynames' => 'json',
        'time_start' => 'date',
        'time_end' => 'date',
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
            ->select(['yz_coupon.id', 'yz_coupon.name', 'yz_coupon.coupon_method', 'yz_coupon.deduct', 'yz_coupon.discount', 'yz_coupon.enough', 'yz_coupon.use_type', 'yz_coupon.category_ids',
                'yz_coupon.categorynames', 'yz_coupon.goods_ids', 'yz_coupon.goods_names', 'yz_coupon.time_limit', 'yz_coupon.time_days', 'yz_coupon.time_start', 'yz_coupon.time_end', 'yz_coupon.get_max', 'yz_coupon.total',
                'yz_coupon.money', 'yz_coupon.credit'])
            ->where('yz_coupon.get_type', '=', 1)
            ->where('yz_coupon.status', '=', 1)
            ->where('yz_coupon.get_max', '!=', 0)
            // 优惠券的level_limit改为存储yz_member_level表的id，所以要关联yz_member_level表
            //->memberLevel($memberLevel);
            ->join('yz_member_level','yz_coupon.level_limit','=','yz_member_level.id')
            ->where(function ($query) use ($memberLevel) {
                $query->where('yz_member_level.level','<=',\app\common\models\MemberLevel::find($memberLevel)->level)
                    ->orWhere('yz_coupon.level_limit', -1);
            });

        if (!is_null($couponId)) {
            $res = $res->where('id', '=', $couponId);
        }

        if (!is_null($time)) {
            $res = $res->unexpired($time);
        }

        return $res->withCount(['hasManyMemberCoupon'])
            ->withCount(['hasManyMemberCoupon as member_got' => function ($query) use ($memberId) {
                return $query->where('uid', '=', $memberId);
            }]);
    }


}
