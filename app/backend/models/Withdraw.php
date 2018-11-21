<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/7/27 下午4:11
 * Email: livsyitian@163.com
 */

namespace app\backend\models;


use app\common\scopes\UniacidScope;

class Withdraw extends \app\common\models\Withdraw
{
    public static function boot()
    {
        parent::boot();
        self::addGlobalScope('uniacid', new UniacidScope);
    }


}
