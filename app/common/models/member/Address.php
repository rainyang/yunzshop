<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/3/25
 * Time: 下午1:48
 */

namespace app\common\models\member;


use app\common\models\BaseModel;

class Address extends BaseModel
{
    public $table = 'yz_address';

    public $timestamps = false;

    public $guarded = [''];

    /*
     * 获取地址数据表全部数据
     *
     * @return array */
    public static function getAllAddress()
    {
        return static::all()->toArray();
    }

}
