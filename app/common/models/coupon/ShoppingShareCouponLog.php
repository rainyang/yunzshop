<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/20
 * Time: 10:50
 */

namespace app\common\models\coupon;


use app\common\models\BaseModel;
use app\framework\Database\Eloquent\Builder;

class ShoppingShareCouponLog extends BaseModel
{
    public $table = 'yz_shopping_share_coupon_log';

    protected $guarded = ['id'];


    //分享者
    public function scopeShareUid(Builder $query, $uid)
    {
        return $query->where('share_uid', $uid);
    }

    //领取者
    public function scopeReceiveUid(Builder $query, $uid)
    {
        return $query->where('receive_uid', $uid);
    }
}