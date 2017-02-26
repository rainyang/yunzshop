<?php

namespace app\backend\modules\goods\models;


/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/2/22
 * Time: 下午2:24
 */
class Category extends \app\common\models\Category
{

    /**
     * @param $category 分类数组
     * @param $uniacid
     */
    public static function editAllCategorys($categorys, $uniacid)
    {
        //@todo 事务处理
        foreach ($categorys as $category) {
            self::where('uniacid', $uniacid)
                ->where('id', $category['id'])
                ->update(['parent_id' => $category['parent_id'], 'display_order' => $category['display_order'], 'level' => $category['level']]);
        }
    }

    /**
     * @param $ids array
     * @param $uniacid
     * @return mixed
     */
    public static function delCategorys($ids, $uniacid)
    {
        $data = self::whereNotIn('id', $ids)
            ->where('uniacid', $uniacid)
            ->delete();
        return $data;
    }

    /**
     * @param $category
     * @return mixed
     */
    public static function saveAddCategory($category)
    {
        $data = self::insert($category);
        return $data;
    }
}