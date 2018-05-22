<?php

namespace app\common\models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class MemberCoupon extends BaseModel
{
    use SoftDeletes;

    public $table = 'yz_member_coupon';

    public $timestamps = false;

    public $dates = ['deleted_at'];


    protected $casts = ['get_time' => 'date'];

    protected $guarded = [];

    protected $appends = ['time_start', 'time_end'];
    public $selected;




    /*
     *  定义字段名
     * @return array */
    public function atributeNames()
    { //todo typo
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
            'back' => '返现',
            'back_time' => '返现时间',
        ];
    }

    public function getTimeStartAttribute()
    {

        if ($this->belongsToCoupon->time_limit == false) {
            $result = $this->get_time;
        } else {
            $result = $this->belongsToCoupon->time_start;
        }

        return $result->toDateString();
    }

    public function getTimeEndAttribute()
    {
        if ($this->belongsToCoupon->time_limit == false) {
            if ($this->belongsToCoupon->time_days == false) {
                $result = '不限时间';
            } else {

                $result = $this->get_time->addDays($this->belongsToCoupon->time_days);
            }
        } else {
            $result = $this->belongsToCoupon->time_end;
        }
        if ($result instanceof Carbon) {
            $result = $result->toDateString();
        }
        return $result;


    }

    /*
     * 字段规则
     * @return array */
    public function rules()
    {
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

    public function scopeCoupons($order_builder, $params)
    {
        $order_builder->with([
                'belongsToCoupon' => function ($query) {
                    $query->where('status', 0);
                }
            ]
        )->where('used', 0);
    }

    public static function getMemberCoupon($MemberModel, $param = [])
    {
        return static::with(['belongsToCoupon' => function ($query) use ($param) {
            if (isset($param['coupon']['coupon_method'])) {
                //$query->where('coupon_method', $param['coupon']['coupon_method']);
            }
            return $query->where('status', 0);
        }])->where('member_id', $MemberModel->uid)->where('used', 0);
    }

    public static function getExpireCoupon()
    {
        $model = self::uniacid();
        $model->where('used', 0);
        return $model;
    }

    public static function getCouponBycouponId($couponId)
    {
        return self::uniacid()
            ->where('coupon_id',$couponId);
    }
}
