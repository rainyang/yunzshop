<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/7
 * Time: 上午9:53
 */

namespace app\common\models;


class UniAccount extends BaseModel
{
    protected $guarded = [];
    public $table = 'uni_account';
    public $primaryKey = 'uniacid';

    public static function checkIsExistsAccount($uniacid)
    {
        return self::find($uniacid);
    }
}