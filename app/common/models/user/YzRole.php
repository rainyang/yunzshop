<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 07/03/2017
 * Time: 10:40
 */

namespace app\common\models\user;


use app\common\models\BaseModel;

class YzRole extends BaseModel
{
    public $table = 'yz_role';


    public function UserPermistions()
    {
        return $this->hasMany('app\common\models\user\YzPermission');
    }

    public static function getRoleList($pageSize)
    {
        return static::uniacid()
            ->paginate($pageSize)
            ->toArray();
    }
}