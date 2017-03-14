<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/14
 * Time: 下午8:34
 */

namespace app\frontend\modules\order\models;



use app\frontend\modules\order\observers\OrderAddressObserver;

class OrderAddress extends \app\common\models\OrderAddress
{
    public static function boot()
    {
        parent::boot();

        //static::$booted[get_class($this)] = true;
        // 开始事件的绑定...
        //creating, created, updating, updated, saving, saved,  deleting, deleted, restoring, restored.
        /*static::creating(function (Eloquent $model) {
            if ( ! $model->isValid()) {
                // Eloquent 事件监听器中返回的是 false ，将取消 save / update 操作
                return false;
            }
        });*/

        //注册观察者
        static::observe(new OrderAddressObserver());
    }
}