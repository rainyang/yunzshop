<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/1/18
 * Time: 下午2:21
 */

namespace app\frontend\models;

use Illuminate\Database\Eloquent\Builder;

class AnotherPayOrder extends Order
{
    public static function boot()
    {
        parent::boot();

        //找人代付
        $uid = \YunShop::request()->mid?:null;

        self::addGlobalScope(function(Builder $query) use ($uid){
            return $query->uid($uid)->where('is_member_deleted',0);
        });
    }
}