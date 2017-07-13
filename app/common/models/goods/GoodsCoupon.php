<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/7/13
 * Time: 下午3:10
 */

namespace app\common\models\goods;


use app\common\models\BaseModel;

class GoodsCoupon extends BaseModel
{
    public $table = 'yz_goods_coupon';
    public $attributes = [

    ];

    public static function getGoodsCouponByGoodsId($goodsId)
    {
        return self::where('goods_id',$goodsId);
    }

}