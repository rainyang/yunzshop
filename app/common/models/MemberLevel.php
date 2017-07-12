<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/27
 * Time: 上午11:18
 */

namespace app\common\models;



use app\common\scopes\UniacidScope;
use Illuminate\Database\Eloquent\SoftDeletes;

class MemberLevel extends BaseModel
{
    use SoftDeletes;

    public $table = 'yz_member_level';

    protected $guarded = [''];

    /**
     * 设置全局作用域 拼接 uniacid()
     */
    public static function boot()
    {
        parent::boot();
        static::addGlobalScope('uniacid',new UniacidScope);
    }

    public function scopeRecords($query)
    {
        return $query->select('id','level','level_name');
    }

    /**
     * 获取默认等级
     *
     * @return mixed
     */
    public static function getDefaultLevelId()
    {
        return self::select('id')
            ->uniacid()
            ->where('is_default', 1);
    }

}
