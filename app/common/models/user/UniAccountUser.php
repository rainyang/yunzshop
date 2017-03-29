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

    protected $guarded = [''];

    public function hasUser()
    {
        return $this->hasMany('app\common\models\user\User', 'uid', 'uid');
    }

    public function hasRole()
    {
        return $this->hasOne('app\common\models\user\YzUserRole', 'user_id', 'uid');
    }



}