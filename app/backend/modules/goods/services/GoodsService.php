<?php
/**
 * Created by PhpStorm.
 * User: Rui
 * Date: 2017/2/22
 * Time: 19:41
 */

namespace app\backend\modules\goods\services;


use app\common\models\GoodsCategory;

class GoodsService
{

    public static function saveGoodsCategory($goodsModel, $categorys, $shopset)
    {
        $category_id = $shopset['cat_level'] == 3 ? $categorys['thirdid'] : $categorys['childid'];
        $goodsCategory = [
            'goods_id' => $goodsModel->id,
            'category_id' => $category_id,
            'category_ids' => implode(',', $categorys),
        ];
        $goodsCategoryModel = new GoodsCategory($goodsCategory);
        return $goodsModel->hasManyGoodsCategory()->save($goodsCategoryModel);
    }

    /**
     * @param $goods
     * @return string
     */
    public static function getList($goods)
    {
        return '';
    }

}