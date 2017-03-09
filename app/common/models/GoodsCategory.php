<?php
/**
 * Created by PhpStorm.
 * User: RainYang
 * Date: 2017/2/22
 * Time: 19:35
 */

namespace app\common\models;

use app\common\models\BaseModel;

class GoodsCategory extends BaseModel
{
    public $table = 'yz_goods_category';

    public $guarded = [];


    public function goods()
    {
        return $this->hasOne('app\common\models\Goods','id','goods_id');
    }



}