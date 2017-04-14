<?php
/**
 * Created by PhpStorm.
 * User: RainYang
 * Date: 2017/2/22
 * Time: 19:35
 */

namespace app\common\models;


use app\common\exceptions\AppException;
use app\frontend\modules\discount\services\models\GoodsDiscount;

class GoodsOption extends \app\common\models\BaseModel
{
    public $table = 'yz_goods_option';

    public $guarded = [];
    public $timestamps = false;

    public function getVipPriceAttribute()
    {
        return GoodsDiscount::getOptionVipPrice($this);
    }
    public function reduceStock($num)
    {
        //拍下立减
        if ($this->goods->reduce_stock_method != 2) {
            if ($this->stock - $num < 0) {
                throw new AppException('下单失败,商品:' . $this->title . ' 库存不足');
            }
            $this->stock -= $num;
        }
    }
    public function goods()
    {
        return $this->belongsTo(Goods::class,'goods_id','id');
    }
}