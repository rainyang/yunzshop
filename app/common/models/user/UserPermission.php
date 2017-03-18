<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 02/03/2017
 * Time: 18:27
 */

namespace app\common\models\user;


use app\common\models\BaseModel;

class UserPermission extends BaseModel
{
    public $table = 'users_permission';

    final function addUserPermission($userId)
    {
        return $this->insert([
            'uniacid' => \YunShop::app()->uniacid,
            'uid' => $userId,
            'type' => 'sz_yi',
            'permission' => 'all',
            'url' => ''
        ]);
    }
}