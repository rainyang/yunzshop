<?php

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/11/9
 * Time: 下午3:09
 */

namespace app\common\models\notice;


use app\common\models\BaseModel;
use app\common\scopes\UniacidScope;
use Illuminate\Database\Eloquent\Builder;
use app\backend\models\BackendModel;

class MinAppTemplateMessage extends BackendModel
{
    public $table = 'yz_min_app_template_message';


    protected $guarded = [''];

    public static function getList()
    {
        return self::select('*')->get();
    }

    public static function getTemp($id)
    {
        return self::select()->where('id',$id)->first();
    }
    public static function getOpenTemp($id)
    {
        return self::select()->where('id',$id)->where('is_default',1)->first();
    }
    public static function delTempDataByTempId($id)
    {
        return self::where('id',$id)->delete();
    }

    public static function getTempById($temp_id)
    {
        return self::select('template_id')->whereId($temp_id);
    }
    public static function getTemplate($id)
    {
        return self::select('template_id')->where('id',$id)->first();
    }

    public static function fetchTempList($kwd)
    {
        return self::select()->where('is_default',0)->likeTitle($kwd);
    }

    public function scopeLikeTitle($query, $kwd)
    {
        return $query->where('title', 'like', '%' . $kwd . '%');
    }

}