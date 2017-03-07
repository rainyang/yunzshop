<?php
/**
 * Created by PhpStorm.
<<<<<<< HEAD
 * User: RainYang
 * Date: 2017/2/22
 * Time: 19:35
=======
 * User: yanglei
 * Date: 2017/2/28
 * Time: 上午11:31
>>>>>>> 8cd399a5a5fe4f2aecc9117c987f889cb5350423
 */

namespace app\common\models;

use app\common\models\BaseModel;

class GoodsCategory extends BaseModel
{
    public $table = 'yz_goods_category';

    public $guarded = [];


    public function hasManyGoods()
    {
        //return $this->hasMany('app\common\models\Goods', 'goods_id', );
    }

}