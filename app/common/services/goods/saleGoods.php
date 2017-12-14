<?php
/**
 * Created date 2017/12/14 10:03
 * Author: 芸众商城 www.yunzshop.com
 */

namespace app\common\services\goods;

use app\common\models\Goods;
use app\common\models\Sale;

class SaleGoods extends Sale
{

    /**
     * 获取推广商品
     * @param  [int] $goods_id [商品id]
     * @return [array]         [推广的商品数据]
     */
    public static function getPushGoods($goods_id)
    {
        $data = self::where('goods_id', $goods_id)->first();

        if ($data->is_push == 1) {
            $goods_ids = explode('-', $data->push_goods_ids);
            $data->push_goods_ids = Goods::getPushGoods($goods_ids);
        } else {
            $data->push_goods_ids = array();
        }

        return $data->push_goods_ids;
    }
}