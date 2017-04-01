<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/11
 * Time: 上午10:00
 */

namespace app\frontend\modules\discount\services\models;

use app\common\models\Goods;
use app\common\models\GoodsOption;

class GoodsDiscount extends Discount
{

    public function getDiscountPrice()
    {

    }

    public static function getVipPrice(Goods $goodsModel)
    {
        return $goodsModel->price * 0.9;
    }

    public static function getOptionVipPrice(GoodsOption $goodsOption)
    {
        return $goodsOption->product_price * 0.9;
    }
}