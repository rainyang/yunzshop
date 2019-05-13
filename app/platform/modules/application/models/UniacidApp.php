<?php

namespace app\platform\modules\application\models;

use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use app\platform\modules\application\models\AppUser;

class UniacidApp extends BaseModel
{
	use SoftDeletes;
	
	protected $table = 'yz_uniacid_app';
	protected $search_fields = ['name', 'validity_time'];
  	protected $guarded = [''];
  	protected $hidden = ['deleted_at', 'updated_at', 'created_at',
                         'type', 'kind', 'title', 'description', 'version'];
    protected $appends = ['status_name', 'is_expire'];

  	
  	public function scopeSearch($query, $keyword)
  	{
        if (!$keyword) {
            return $query;
        }

  		if ($keyword['name']) {
  			$query = $query->where('name', 'like', '%'.$keyword['name'].'%');
  		}

  		if ($keyword['maturity']) {
  			
  			if ($keyword['maturity'] == 1) {
  				// 到期
	  			$query = $query->where('validity_time', '<>', 0)->where('validity_time',  '<', mktime(0,0,0, date('m'), date('d'), date('Y')));
	  		}

	  		if ($keyword['maturity'] == 2) {
	  			$query = $query->where('validity_time', 0)->Orwhere('validity_time', '>=', mktime(0,0,0, date('m'), date('d'), date('Y')));
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
            'description' => "应用描述",
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
            'description' => '',
            'status' => '',
            'version' => '',
            'validity_time' => 'numeric',
        ];
    }

    public function getStatusNameAttribute()
    {
    	return ['禁用', '启用'][$this->status];
    }

    public function getIsExpireAttribute()
    {
        //到期前一周的时间  当前+1 直到 +7 小于等于 $value['validity_time']
        $week = date('W');

        $nowstamp = mktime(0,0,0, date('m'), date('d'), date('Y') );

        $time_week = date('W', 'validity_time');

        if ((date('W', strtotime('+1 week')) == $time_week) || (date('W') == $time_week && 'validity_time' >= $nowstamp)) {
            
            $this->is_expire = 1;  //到期前一周
        } 

        if ('validity_time' != 0 && 'validity_time' < $nowstamp) {
            
            $this->is_expire = 2;  //已经到期
        } 

        if('validity_time' === 0 || ( date('W', strtotime('+1 week') - $time_week > 1) && 'validity_time' > $nowstamp) ) {
            $this->is_expire = 0;
        }
        return $this->is_expire;
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

    public function hasOneAdminUser()
    {
        return $this->hasOne(\app\platform\modules\user\models\AdminUser::class, 'uid', 'creator');
    }

}