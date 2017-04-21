<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/2
 * Time: 上午11:24
 */

namespace app\common\models;

use app\common\models\goods\GoodsDispatch;
use app\common\models\order\OrderGoodsChangePriceLog;

class OrderGoods extends BaseModel
{
    public $table = 'yz_order_goods';
    protected $hidden = ['order_id'];

    protected $fillable = [];
    protected $guarded = ['id'];
    protected $attributes = [
        'goods_option_id' => 0,
        'goods_option_title' => ''
    ];
    protected $search_fields = ['goods_sn', 'title'];

    //public function
    public function hasOneGoods()
    {
        return $this->hasOne('\app\common\models\Goods', 'id', 'goods_id');
    }

    public function goods()
    {
        return $this->hasOne('\app\common\models\Goods', 'id', 'goods_id');
    }

    public function hasOneGoodsDispatch()
    {
        return $this->hasOne(GoodsDispatch::class, 'goods_id', 'goods_id');
    }

    public function belongsToGood()
    {
        return $this->belongsTo('\app\common\models\Goods', 'goods_id', 'id');
    }

    public function goodsOption()
    {
        return $this->hasOne('\app\common\models\GoodsOption', 'id', 'goods_option_id');

    }

    public function orderGoodsChangePriceLogs()
    {
        return $this->hasMany(OrderGoodsChangePriceLog::class, 'order_id', 'id');

    }

    public function isOption()
    {
        return !empty($this->goods_option_id);
    }


}