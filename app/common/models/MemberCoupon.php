<?php

namespace app\common\models;

use Illuminate\Database\Eloquent\Model;

class MemberCoupon extends BaseModel
{
    public $table = 'yz_member_coupon';

    public function belongsToCoupon()
    {
        return $this->belongsTo('app\common\models\Coupon', 'coupon_id', 'id');
    }

    public static function getMemberCoupon($MemberModel)
    {
        return static::with(['belongsToCoupon' => function ($query) {
            return $query->where('status', 0);
        }])->where('member_id', $MemberModel->uid)->where('used', 0);
    }
}
