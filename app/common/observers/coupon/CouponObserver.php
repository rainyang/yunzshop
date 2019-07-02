<?php
/**
 * Created by PhpStorm.
 * User: win 10
 * Date: 2019/7/2
 * Time: 10:39
 */

namespace app\common\observers\coupon;

use app\common\models\Coupon;
use app\common\observers\BaseObserver;
use Illuminate\Database\Eloquent\Model;
use Yunshop\Hotel\common\models\CouponHotel;

class CouponObserver extends BaseObserver
{
    public function created(Model $model)
    {
        if($model->widgets['more_hotels'] && $model->use_type == Coupon::COUPON_MORE_HOTEL_USE){
            $couponHotel = new CouponHotel();
            $arr = $model->widgets['more_hotels'];
            $couponHotel->fill([
                'coupon_id' => $model->id,
                'hotel_ids' => $arr
            ]);
            $couponHotel->save();
        }
    }


    public function updated(Model $model)
    {
        if($model->widgets['more_hotels'] && $model->use_type == Coupon::COUPON_MORE_HOTEL_USE){
            $couponHotel = CouponHotel::where('coupon_id',$model->id)->first();
            if(!$couponHotel){
                return;
            }
            $arr = $model->widgets['more_hotels'];
            $couponHotel->fill([
                'coupon_id' => $model->id,
                'hotel_ids' => $arr
            ]);
            $couponHotel->save();
        }
    }


    public function deleted(Model $model)
    {
        CouponHotel::where([
            'coupon_id' => $model->id,
        ])->delete();
    }
}