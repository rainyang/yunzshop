<?php
/**
 * Created by PhpStorm.
 * User: Rui
 * Date: 2017/3/1
 * Time: 09:41
 */

namespace app\common\models;


class GoodsSpecItem extends \app\common\models\BaseModel
{
    public $table = 'yz_goods_spec_item';

    public $guarded = [];

    //public $timestamps = true;

    public function hasManyOption()
    {
        return $this->hasMany('app\common\models\GoodsOption');
    }
}