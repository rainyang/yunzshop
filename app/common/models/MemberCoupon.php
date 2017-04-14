<?php

namespace app\common\models;


class MemberCoupon extends BaseModel
{
    public $table = 'yz_member_coupon';
    protected $guarded = [''];

    /*
     *  定义字段名
     * @return array */
    public function atributeNames() { //todo typo
        return [
            'uniacid' => '公众号 ID',
            'uid' => '用户 ID',
            'coupon_id' => '优惠券 ID',
            'get_type' => '获取优惠券的方式',
            'used' => '是否已经使用',
            'use_time' => '使用优惠券的时间',
            'get_time' => '获取优惠券的时间',
            'send_uid' => '手动发放优惠券的操作人员的 uid',
            'order_sn' => '使用优惠券的订单号',
            'back'  => '返现',
            'back_time' => '返现时间',
        ];
    }

    /*
     * 字段规则
     * @return array */
    public function rules() {
        return [
            'uniacid' => 'required|integer',
            'uid' => 'required|integer',
            'coupon_id' => 'required|integer',
            'get_type' => 'integer|between:0,2',
            'used' => 'integer|between:0,1',
            'use_time' => 'numeric',
            'get_time' => 'required|numeric',
            'send_uid' => 'integer',
            'order_sn' => 'string',
//            'back'  => '',
            'back_time' => 'numeric',
        ];
    }


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
