<?php
namespace app\common\models;


/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/2/27
 * Time: 上午9:11
 */
class Street extends BaseModel
{

    public $table = 'yz_street';

    protected $guarded = [''];

    protected $fillable = [''];


    public static function getStreetByParentId($parentId)
    {
        return self::where('parentid', $parentId)
            ->where('level', '4')
            ->get();
    }

    
}
