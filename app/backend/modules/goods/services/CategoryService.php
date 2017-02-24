<?php

namespace app\backend\modules\goods\services;
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/2/22
 * Time: 下午2:53
 */

class CategoryService
{
    /**
     * @param $categorys array
     * @return mixed
     */
    public static function getLists($categorys)
    {

        foreach ($categorys as $row) {
            if (empty($row->parent_id)) {
                $data['parents'][] =   $row;
            } else {
                $data['childrens'][$row['parent_id']][] = $row;
            }
        }
        return $data;
    }

    /**
     * @param $categorys array
     * @param $uniacid
     * @return mixed
     */
    public static function saveCategory($categorys, $uniacid)
    {
        if( isset($categorys['parent_id']) ) {
            //子分类
            $categorys['uniacid'] = $uniacid;
        } else {
            $categorys['uniacid'] = $uniacid;
        }
        return $categorys;
    }

    /**
     * @param $category
     * @return array
     */
    public static function editCategory($category)
    {
        return $item = [
            'id'            => $category->id,
            'name'          => $category->name,
            'thumb'         => $category->thumb,
            'description'   => $category->description,
            'adv_img'       => $category->adv_img,
            'adv_url'       => $category->adv_url,
            'is_home'       => $category->is_home,
            'enabled'       => $category->enabled,
            'display_order' => $category->display_order,
            'level'         => $category->level,
            'parent_id'     => $category->parent_id
        ];
    }

}