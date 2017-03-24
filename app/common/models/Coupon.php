<?php

namespace app\common\models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends BaseModel
{
    public $table = 'yz_coupon';

    public function hasManyMemberCoupon()
    {
        return $this->hasMany('app\common\models\MemberCoupon');
    }
}
