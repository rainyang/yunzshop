<?php
/**
 * Created by PhpStorm.
 * User: RainYang
 * Date: 2017/2/22
 * Time: 19:35
 */

namespace app\common\models;



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
}