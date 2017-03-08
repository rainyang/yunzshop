<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/2
 * Time: 上午11:24
 */

namespace app\common\models;

class OrderGoods extends BaseModel
{
    public $table = 'yz_order_goods';
    protected $search_fields = ['goods_sn','title'];
    protected $hidden = ['order_id'];

    public function hasOneGoods()
    {
        return $this->hasOne('\app\common\models\Goods', 'id', 'goods_id');
    }

    public function belongsToGood()
    {
        return $this->belongsTo('\app\common\models\Goods', 'goods_id', 'id');
    }
}