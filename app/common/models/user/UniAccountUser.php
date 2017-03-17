<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 04/03/2017
 * Time: 14:16
 */

namespace app\common\models\user;


use app\common\models\BaseModel;

class UniAccountUser extends BaseModel
{
    public $table = 'uni_account_users';

    public $timestamps = false;

    public static function getUserList($pageSize)
    {
        //未完成， 需要添加查询角色
        return static::uniacid()
            ->select('uid', 'role')
            ->with(['user' => function($user) {
                return $user->select('uid', 'username', 'status')
                    ->with(['userProfile' => function($profile) {
                        return $profile->select('uid', 'realname', 'mobile');
                    }]);
            }])
            ->paginate($pageSize)->toArray();
    }

    public function user()
    {
        return $this->hasMany('app\common\models\user\User', 'uid', 'uid');
    }

    public function relationValidator()
    {

    }


}