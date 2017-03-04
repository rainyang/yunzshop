<?php
/**
 * Created by PhpStorm.
 * User: luckystar_D
 * Date: 2017/3/4
 * Time: 上午11:23
 */

namespace app\common\models;


use Illuminate\Database\Eloquent\Model;

class Area   extends Model
{
    public $table = 'yz_address';

    protected $guarded = [''];

    public static function getAreaList()
    {
        return self::get();
    }

    public static function getProvinces($parentId)
    {
        return self::where('parentid', $parentId)
            ->get();
    }

    public static function getCitysByProvince($parentId)
    {
        return self::where('parentid', $parentId)
            ->get();
    }

    public static function getAreasByCity($parentId)
    {
        return self::where('parentid', $parentId)
            ->get();
    }
}