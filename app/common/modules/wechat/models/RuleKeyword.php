<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/6
 * Time: 下午9:09
 */

namespace app\common\modules\wechat\models;

use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class RuleKeyword extends BaseModel
{
    //public $table = 'yz_wechat_rule_keyword';
    public $table = 'rule_keyword';

    //use SoftDeletes;
    protected $guarded = [''];
    /**
     * 字段规则
     *
     * @return array
     */
    public function rules()
    {
        return [
            'rid' => 'required|numeric',
            'uniacid' => 'required|numeric',
            'module' => 'required',
            'content' => 'required',
            'type' => 'numeric|required',
            'displayorder' => 'numeric',
            'status' => 'numeric',
        ];
    }

    /**
     * 定义字段名
     *
     * @return array
     */
    public function atributeNames()
    {
        return [
            'rid' => '规则id',
            'uniacid' => '公众号id',
            'module' => '模块',
            'content' => '关键字内容',
            'type' => '触发类型',
            'displayorder' => '回复优先级',
            'status' => '是否开启',
        ];
    }

    // 通过id获取模型对象
    public static function getKeywordsInfo($id)
    {
        if (empty($id)) {
            return null;
        } else {
            return static::uniacid()->select('id','content','rid')->with('hasOneRule')->find($id);
        }
    }
    public function hasOneRule()
    {
        return $this->hasOne(Rule::class,'id','rid')->select('id','name');
    }

    // 获取所有关键字
    public static function getRuleKeywords()
    {
        return static::uniacid()->get();
    }

    // 通过id获取模型对象
    public static function getRuleKeywordById($id)
    {
        return static::uniacid()->find($id);
    }

    // 通过rid获取多个关键字对象
    public static function getRuleKeywordsByRid($rid)
    {
        return static::uniacid()->where('rid',$rid)->get();
    }

    // 通过rid获取多个关键字id
    public static function getRuleKeywordIdsByRid($rid)
    {
        return static::select('id')->uniacid()->where('rid',$rid)->get();
    }

    // 通过关键字获取规则
    public static function getRuleKeywordByKeywords($keywords)
    {
        // 先找精准触发
        $accurate = static::uniacid()->where('status','=',1)
            ->where('content','=',$keywords)
            ->where('type','=',1)
            ->orderBy('displayorder','desc')
            ->first();

        // 再找模糊查询,正则匹配先不考虑
        if (empty($accurate)) {
            return static::uniacid()->where('status','=',1)
                ->where('content','like',$keywords.'%')
                ->where('type','!=',1)
                ->orderBy('displayorder','desc')
                ->first();
        } else {
            return $accurate;
        }
    }

}