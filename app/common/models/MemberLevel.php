<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/2/27
 * Time: 上午11:18
 */

namespace app\common\models;



use Illuminate\Database\Eloquent\SoftDeletes;

class MemberLevel extends BaseModel
{
    use SoftDeletes;

    public $table = 'yz_member_level';

    protected $guarded = [''];

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
