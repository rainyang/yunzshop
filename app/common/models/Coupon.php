<?php

namespace app\common\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends BaseModel
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    const COUPON_ORDER_USE = 1;
    const COUPON_ALL_USE = 2;
    const COUPON_CATEGORY_USE = 3;
    const COUPON_GOODS_USE = 4;
    const COUPON_MONEY_OFF = 1;
    const COUPON_DISCOUNT = 3;
    const COUPON_DATE_TIME_RANGE = 0;
    const COUPON_SINCE_RECEIVE = 1;

    public $table = 'yz_coupon';

    protected $guarded = [];


    protected $casts = [
        'goods_ids' => 'json',
        'categoryids' => 'json',
        'goods_names' => 'json',
        'categorynames' => 'json',
        'time_start' => 'datetime',
        'time_end' =>'datetime',
    ];

    protected $attributes = [
        'goods_ids' => [],
        'categoryids' => [],
    ];

    public static function getMemberCoupon($used = 0) { //todo 这张表没有used这个字段, 应该放在member_coupon表?
        return static::uniacid()->where('used', $used);
    }

    public function hasManyMemberCoupon()
    {
        return $this->hasMany('app\common\models\MemberCoupon');
    }

    public static function getValidCoupon($MemberModel)
    {
        return MemberCoupon::getMemberCoupon($MemberModel);
    }

    public static function getUsageCount($couponId)
    {
        return static::uniacid()
                    ->select(['id'])
                    ->where('id', '=', $couponId)
                    ->withCount(['hasManyMemberCoupon' => function($query){
                        return $query->where('used', '=', 0);
                    }]);
    }

    public static function getCouponById($couponId)
    {
        return static::uniacid()
                    ->where('id', '=', $couponId);
    }

    //getter
    public static function getter($couponId, $attribute)
    {
        return static::uniacid()
            ->where('id', '=', $couponId)
            ->value($attribute);
    }
}
