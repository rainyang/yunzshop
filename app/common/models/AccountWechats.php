<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2017/4/5
 * Time: 上午10:00
 */

namespace app\common\models;

class AccountWechats extends BaseModel
{
    public $table = 'account_wechats';

    public static function getAccountInfoById($id)
    {
        return self::uniacid()
            ->where('acid', $id)
            ->first();
    }
}