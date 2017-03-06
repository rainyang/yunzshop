<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/2
 * Time: 上午11:24
 */

namespace app\common\models;


use Illuminate\Database\Eloquent\Model;

class OrderGoods extends Model
{
    public $table = 'yz_order_goods';
    public function hasOneGoods()
    {
        return $this->hasOne('\app\common\models\Goods', 'id', 'goods_id');
    }
    public function belongsToGood()
    {
        return $this->belongsTo('\app\common\models\Goods', 'goods_id', 'id');
    }
}