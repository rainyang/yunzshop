<?php

namespace app\common\models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends BaseModel
{
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

    public static function getMemberCoupon($used = 0) { //todo 没有used这个字段
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
}
