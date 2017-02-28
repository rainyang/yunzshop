<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/2/22
 * Time: 下午4:53
 */

/**
 * 会员表
 */
namespace app\frontend\modules\member\models;

use Illuminate\Database\Eloquent\Model;

class MemberModel extends Model
{
    public $table = 'mc_members';

    public static function getId($uniacid, $mobile)
    {
        return self::where('uniacid', $uniacid)->where('mobile', $mobile)->get();
    }

    public static function insertData($data)
    {
        return self::insertGetId($data);
    }
}