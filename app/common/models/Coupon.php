<?php

namespace app\common\models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends BaseModel
{
    const COUPON_ORDER_USE = 1;
    const COUPON_ALL_USE = 2;
    const COUPON_CATEGORY_USE = 3;
    const COUPON_GOODS_USE = 4;

    public $table = 'yz_coupon';

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
