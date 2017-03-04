<?php
namespace app\common\models;

use app\backend\models\BackendModel;

/**
 * Created by PhpStorm.
 * User: jan
 * Date: 21/02/2017
 * Time: 12:58
 */
class Member extends BackendModel
{
    public $table = 'mc_members';


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