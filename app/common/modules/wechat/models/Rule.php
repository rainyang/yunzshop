<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/6
 * Time: 下午9:09
 */

namespace app\common\modules\wechat\models;

use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Rule extends BaseModel
{
    //public $table = 'yz_wechat_rule';
    public $table = 'rule';

    //use SoftDeletes;
    protected $guarded = [''];
    public $timestamps = false;
    // 关键字类型
    const REPLY_TYPE_BASIC = 'basic';
    const REPLY_TYPE_IMAGE= 'images';
    const REPLY_TYPE_MUSIC = 'music';
    const REPLY_TYPE_NEWS = 'news';
    const REPLY_TYPE_USERAPI = 'userapi';
    const REPLY_TYPE_VIDEO = 'video';
    const REPLY_TYPE_VOICE = 'voice';

    const MODULE_INDEX = 1;


    public function rules()
    {
        return [
            'uniacid' => 'required',
            'name' => 'required',
            'module' => 'required',
            'displayorder' => 'numeric|min:0|max:254',
            'status' => 'numeric',
            'containtype' => 'required',
            'reply_type' => 'numeric|min:0',
        ];
    }

    public function atributeNames()
    {
        return [
            'uniacid' => '公众号ID',
            'name' => '规则',
            'module' => '模块',
            'displayorder' => '回复优先级',
            'status' => '状态',
            'containtype' => '回复内容类型',
            'reply_type' => '回复类型',
        ];
    }

    // 通过id获取模型对象
    public static function getRuleById($id)
    {
        return static::uniacid()->find($id);
    }

    // 通过id获取规则及关键字
    public static function getRuleAndKeywordsByRuleId($id)
    {
        return static::uniacid()->with('hasManyKeywords')->find($id);
    }

    public function hasManyKeywords()
    {
        return $this->hasMany(RuleKeyword::class,'rid','id');
    }
}