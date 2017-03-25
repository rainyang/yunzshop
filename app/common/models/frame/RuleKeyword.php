<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/3/23
 * Time: 上午10:32
 */

namespace app\common\frame;


use app\common\models\BaseModel;

class RuleKeyword extends BaseModel
{
    public $table = 'rule_keyword';

    public $timestamps = false;

    protected $guarded = [''];


    protected static $module = 'sz_yi';


    /*
     * 关键字是否存在，存在返回id，不存在返回false；
     *
     * @param string $keyword
     *
     * @return mixed   $id or false
     * */
    public static function hasKeyword($keyword)
    {
        $id = self::select('id')->uniacid()->where('module', static::$module)->where('content', $keyword)->first();

        return empty($id) ? false : $id;
    }

}
