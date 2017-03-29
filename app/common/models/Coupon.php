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
    const COUPON_DISCOUNT = 2;

    public $table = 'yz_coupon';
    protected $casts = [
        'goods_ids' => 'json',
        'categoryids' => 'json'
    ];
    protected $attributes = [
        'goods_ids' => [],
        'categoryids' => [],
    ];
    public static function getMemberCoupon($used = 0) {
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
