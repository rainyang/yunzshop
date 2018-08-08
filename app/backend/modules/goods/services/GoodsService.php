<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
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

    public static function saveGoodsMultiCategory($goodsModel, $categorys, $shopset)
    {
        if (!empty($categorys)) {
            foreach ($categorys['parentid'] as $key => $val) {
                switch ($shopset['cat_level']) {
                    case 2:
                        $category_id = $categorys['childid'][$key];
                        $category_ids = $val . ',' . $categorys['childid'][$key];
                        break;
                    case 3:
                        $category_id = $categorys['thirdid'][$key];
                        $category_ids = $val . ',' . $categorys['childid'][$key] . ',' . $categorys['thirdid'][$key];
                        break;
                    default:
                        $category_id = $categorys['childid'][$key];
                        $category_ids = $val . ',' . $categorys['childid'][$key];
                }

                $goodsCategory = [
                    'goods_id' => $goodsModel->id,
                    'category_id' => $category_id,
                    'category_ids' => $category_ids,
                ];
                $goodsCategoryModel = new GoodsCategory($goodsCategory);

                if (!$goodsModel->hasManyGoodsCategory()->save($goodsCategoryModel)) {
                    return false;
                }
            }
        }

        return true;
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