<?php

namespace app\common\models;

use Illuminate\Database\Eloquent\Model;

class MemberCoupon extends BaseModel
{
    public $table = 'yz_member_coupon';

    public function belongsToCoupon()
    {
        $this->belongsTo('app\common\models\Coupon');
    }
}
