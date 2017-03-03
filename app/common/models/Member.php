<?php
namespace app\common\models;

use Illuminate\Database\Eloquent\Model;

/**
 * Created by PhpStorm.
 * User: jan
 * Date: 21/02/2017
 * Time: 12:58
 */
class Member extends Model
{
    public $table = 'mc_members';

    public static function getMemberById($uid)
    {
        return self::where('uid', $uid)
            ->first();
    }
    /**
     * @return mixed
     */
    public static function getRandNickName()
    {
        return self::select('nick_name')
            ->whereNotNull('nick_name')
            ->inRandomOrder()
            ->first()
            ->toArray();
    }
    
    /**
     * @return mixed
     */
    public static function getRandAvatar()
    {
        return self::select('avatar')
            ->whereNotNull('avatar')
            ->inRandomOrder()
            ->first()
            ->toArray();
    }
}