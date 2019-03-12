<?php

namespace app\platform\modules\application\models;

use app\common\models\BaseModel;
use app\platform\modules\user\models\AdminUser;
use Illuminate\Database\Eloquent\Model;


class AppUser extends BaseModel
{
	
	protected $table = 'app_user';
	protected $search_fields = [''];
  	protected $guarded = [''];
    protected $hidden = ['deleted_at', 'updated_at', 'created_at'];


    public function atributeNames() 
    {
        return [
            'uniacid' => '',

            'uid' => '',

            'role' => ''
        ];
    }
    
    public function rules()
    {
    	return [
            'uniacid' => 'required | integer',
            'uid' => 'required | integer',
            'role' => 'required | string | max:20',
        ];
    }

    public function hasOneApp()
    {
        return $this->hasOne(\app\platform\modules\application\models\UniacidApp::class, 'id', 'uniacid');
    }

    public function hasOneUser()
    {
        return $this->hasOne(\app\platform\modules\user\models\AdminUser::class, 'id', 'uid');
    }
}