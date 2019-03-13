<?php

namespace app\platform\modules\application\models;

use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use app\platform\modules\user\models\AdminUser;
use app\platform\modules\application\models\AppUser;

class UniacidApp extends BaseModel
{
	use SoftDeletes;
	
	protected $table = 'yz_uniacid_app';
	protected $search_fields = ['name', 'validity_time'];
  	protected $guarded = [''];
  	// protected $dates = ['validity_time'];
  	protected $hidden = ['deleted_at', 'updated_at', 'created_at',
                         'type', 'kind', 'title', 'descr', 'version', 'uniacid'];
    protected $appends = ['status_name'];

  	
  	public function scopeSearch($query, $keyword)
  	{
  		$ids = self::checkRole();

  		if (!is_array($ids)) {
  			return $query;
  		}

  		$query = $query->where('status', 1);
  		// $query = $query->where('id', $ids)->where('status', 1)->get();
		// dd($query);  		
  		if ($keyword['name']) {
  			$query = $query->where('name', 'like', '%'.$keyword['name'].'%');
  		}

  		if ($keyword['maturity']) {
  			
  			if ($keyword['maturity'] == 1) {
  				// 到期
	  			// $query = $query->where(DATE_FORMAT('validity_time', '%Y-%m-%d'),  date('Y-m-d'));
	  			$query = $query->whereDate('validity_time',  date('Y-m-d'));
	  		}

	  		if ($keyword['maturity'] == 2) {
	  			// $query = $query->where(DATE_FORMAT('validity_time', '%Y-%m-%d'), '!=' , date('Y-m-d'));
	  			$query = $query->whereDate('validity_time', '!=' , date('Y-m-d'));
	  		}
  		}

  		return $query;
  	}

    public function atributeNames() 
    {
        return [
            'img'=> "应用图片",
            'url'=> "应用跳转地址",
            'name' => "应用名称",
            'kind' => "行业分类",
            'title' => "应用标题",
            'descr' => "应用描述",
            'version' => "应用版本",
            'type' => '应用类型',
            'status' => "应用状态",
            'validity_time' => "有效期",
        ];
    }
 
    public function rules()
    {
    	return [
            'img' => '',
            'url' => '',
            'name' => 'max:10',
            'kind' => '',
            'type' => '',
            'title' => '',
            'descr' => '',
            'status' => '',
            'version' => '',
            'validity_time' => 'numeric',
        ];
    }

    public function getStatusNameAttribute()
    {
    	return ['禁用', '启用'][$this->status];
    }

    public static function chekcApp($id)
    {
		$app = self::find($id);
		if (!$app || $app->status != 1) {
			return false;
		}
		return true;
    }

    public static function checkRole()
    {
    	$uid = \Auth::guard('admin')->user()->id;

        $user = AdminUser::find($uid);

        $appUser = AppUser::where('uid', $uid)->get();

        if (!$user || !$appUser || $user->type != 1) {
            return '您无权限查看平台应用';
        }

        if ($user->status != 0) {
            return '您的账号已过期';
        }
        
        foreach ($appUser->toArray() as $k => $v) {
        	$ids[] = $v['id'];
        }
        
        return $ids;
    }

}