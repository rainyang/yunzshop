<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 02/03/2017
 * Time: 18:19
 */

namespace app\common\models\user;


use app\common\models\BaseModel;

class User extends BaseModel
{
    public $table = 'users';

    public function uniAccounts()
    {
        return $this->hasMany('app\common\models\user\UniAccountUser', 'uid', 'uid');
    }

    public function userRoles()
    {
        return $this->hasMany('app\common\models\user\YzUserRole', 'user_id', 'uid');
    }

    public function permissions()
    {
        return $this->hasMany('app\common\models\user\YzPermission', 'item_id', 'uid')
            ->where('type', '=', YzPermission::TYPE_USER);
    }

    /**
     * 数据库获取用户权限
     * @return mixed
     */
    public static function getUserPermissionsCache()
    {
        $key = 'user.permissions.'.\YunShop::app()->uid;
        $list = \Cache::get($key);
        if($list === null){
            $list =  static::select(['uid'])
                ->where(['uid' => \YunShop::app()->uid])
                ->where('type','!=', '1')
                ->with([
                    'userRoles' => function ($query) {
                        return $query->select(['user_id','role_id'])
                            ->with(['permissions']);
                    },
                    'permissions'
                ])
                ->get();

            \Cache::put($key,$list,3600);
        }

        return $list;
    }

    /**
     * 获取并组合用户权限
     * @return array
     */
    public static function getAllPermissions()
    {
        $userPermissions = self::getUserPermissionsCache()->toArray();
        $permissions = [];
        if($userPermissions) {
            foreach ($userPermissions as $v) {
                if ($v['permissions']) {
                    foreach ($v['permissions'] as $v1) {
                        $permissions[] = $v1['permission'];
                    }
                }
                if ($v['user_roles']) {
                    foreach ($v['user_roles'] as $v2) {
                        if ($v2['permissions']) {
                            foreach ($v2['permissions'] as $v3) {
                                !in_array($v3['permission'], $permissions) && $permissions[] = $v3['permission'];
                            }
                        }
                    }
                }
            }
        }

        return $permissions;
    }
}