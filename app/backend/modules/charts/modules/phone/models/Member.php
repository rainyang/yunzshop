<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/19
 * Time: 15:59
 */

namespace app\backend\modules\charts\modules\phone\models;


use Illuminate\Database\Eloquent\Builder;

class Member extends \app\common\models\Member
{

    public static function getMember()
    {
        return self::uniacid()->select('uid','mobile','nickname');
    }
}