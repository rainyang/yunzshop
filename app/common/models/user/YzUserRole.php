<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 04/03/2017
 * Time: 14:25
 */

namespace app\common\models\user;


use app\common\models\BaseModel;

class YzUserRole extends BaseModel
{
    public $table = 'yz_user_role';

    public function role()
    {
        return $this->hasOne('app\common\models\user\YzRole','id','role_id');
    }

    public function permissions()
    {
        return $this->hasMany('app\common\models\user\YzPermission','item_id','role_id')
            ->where('type','=', YzPermission::TYPE_ROLE);
    }

}