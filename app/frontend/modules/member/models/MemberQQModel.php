<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/2/23
 * Time: 上午10:47
 */

/**
 * QQ登录表
 */
namespace app\frontend\modules\member\models;

use Illuminate\Database\Eloquent\Model;

class MemberQQModel extends Model
{
    public $table = 'yz_member_qq';

    public static function insertData($data)
    {
        self::insert($data);
    }
}