<?php
namespace app\common\models;


/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/2/27
 * Time: 上午9:11
 */
class Address extends BaseModel
{

    public $table = 'yz_address';

    protected $guarded = [''];

    protected $fillable = [''];


    public static function getProvince()
    {
        return self::where('level', '1')->get();
    }

    public static function getCityByParentId($parentId)
    {
        return self::where('parentid', $parentId)
            ->where('level', '2')
            ->get();
    }

    public static function getAreaByParentId($parentId)
    {
        return self::where('parentid', $parentId)
            ->where('level', '3')
            ->get();
    }
    

}
