<?php

namespace app\common\models;


class MemberCoupon extends BaseModel
{
    public $table = 'yz_member_coupon';

    public function belongsToCoupon()
    {
        return $this->belongsTo('app\common\models\Coupon', 'coupon_id', 'id');
    }

    public function scopeCoupons($order_builder, $params){
        $order_builder->with([
                'belongsToCoupon'=>function($query){
                    $query->where('status',0);
                }
            ]
        )->where('used', 0);
    }

    public static function getMemberCoupon($MemberModel,$param = [])
    {
        return static::with(['belongsToCoupon' => function ($query) use ($param) {
            if(isset($param['coupon']['back_type'])){
                //$query->where('back_type', $param['coupon']['back_type']);
            }
            return $query->where('status', 0);
        }])->where('member_id', $MemberModel->uid)->where('used', 0);
    }
}
