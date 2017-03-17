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

    public function hasUser()
    {
        return $this->hasMany('app\common\models\user\User', 'uid', 'uid');
    }

    public function hasRole()
    {
        return $this->hasOne('app\common\models\user\YzUserRole', 'user_id', 'uid');
    }

    /*
     * 获取操作员列表
     * */
    public static function getUserList($pageSize)
    {
        //未完成， 需要添加查询角色
        return static::uniacid()
            ->select('uid', 'role')
            ->with(['hasUser' => function($user) {
                return $user->select('uid', 'username', 'status')
                    ->with(['userProfile' => function($profile) {
                        return $profile->select('uid', 'realname', 'mobile');
                    }]);
            }])
            ->with(['hasRole' => function($hasRole) {
                return $hasRole->select('user_id', 'role_id')
                    ->with(['role' => function($role) {
                        return $role->select('id','name');
                    }]);
            }])
            ->paginate($pageSize);
    }

    /*
     *  添加操作员,挂件使用
     *
     *  @parms int $userId
     * */
    public function addOperator($userId)
    {
        return $this->insert([
            'uniacid' => \YunShop::app()->uniacid,
            'uid' => $userId,
            'role' => 'operator',
            'rank' => NULL
        ]);
    }


}