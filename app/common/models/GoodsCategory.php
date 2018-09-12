<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/22
 * Time: 19:35
 */

namespace app\common\models;

use app\common\models\BaseModel;

class GoodsCategory extends BaseModel
{
    public $table = 'yz_goods_category';

    public $guarded = ['updated_at', 'created_at', 'deleted_at'];

    public function goods()
    {
        return $this->hasOne('app\common\models\Goods','id','goods_id');
    }

    public function delCategory($goods_id)
    {
        return $this->where(['goods_id' => $goods_id])
            ->delete();
    }


}