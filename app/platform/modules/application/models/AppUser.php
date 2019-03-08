<?php

namespace app\platform\modules\application\models;

use app\common\models\BaseModel;
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
            'name' => "名称",
            'owner_uid' => "创建用户",
            'uniacid' => "所属公众号",
            'app_role_id' => "角色",
            'app_permission_id' => "权限",
            'status' => "应用状态",
        ];
    }
    
    public function rules()
    {
    	return [
            'name' => 'string|max:10',
            'owner_uid' => 'numeric',
            'uniacid' => 'numeric',
            'app_role_id' => '',
            'app_permission_id' => '',
            'status' => 'numeric',
        ];
    }
}