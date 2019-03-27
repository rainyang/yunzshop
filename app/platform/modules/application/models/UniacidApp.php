<?php

namespace app\platform\modules\application\models;

use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

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
  		
  		$query = $query->where('status', 1);

      if (!$keyword) {
        return $query;
      }

  		if ($keyword['name']) {
  			$query = $query->where('name', 'like', '%'.$keyword['name'].'%');
  		}

  		if ($keyword['maturity']) {
  			
  			if ($keyword['maturity'] == 1) {
  				// 到期
	  			$query = $query->where('validity_time',  $keyword['maturity']);
	  		}

	  		if ($keyword['maturity'] == 2) {
	  			$query = $query->where('validity_time', '!=' , $keyword['maturity']);
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

    public static function getApplicationByid($id)
    {
        return self::withTrashed()->where('id', $id)->first();
    }


}