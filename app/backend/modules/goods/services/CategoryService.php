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
                $data[parents][] = $row;
            } else {
                $data[childrens][$row['parent_id']][] = $row;
            }
        }
        return $data;
    }

    /**
     * @param $categorys array
     * @param $uniacid
     * @param string $parent_id
     */
    public static function saveCategory($categorys, $uniacid)
    {
        if( isset($categorys['parent_id']) ) {
            //子分类
        } else {
            $categorys['uniacid'] = $uniacid;
            $categorys['level'] = '1';
        }
        return $categorys;
    }
    
    /**
     * @param $categorys json [{"id":1,"children":[{"id":8}]}]
     * @return mixed
     */
    public static function processCategory($categorys)
    {
        ca('shop.category.edit');
        if (empty($categorys)) {
            message('分类保存失败，请重试!', '', 'error');
        }
        $categorys = json_decode(html_entity_decode($categorys), true);
        if (!is_array($categorys)) {
            message('分类保存失败，请重试!', '', 'error');
        }
        $displayorder = count($categorys);
        $datas = [];
        foreach ($categorys as $category) {
            $datas['cateids'][] = $category['id'];
            $datas['parents'][$category['id']]['id'] = $category['id'];
            $datas['parents'][$category['id']]['parent_id'] = '0';
            $datas['parents'][$category['id']]['display_order'] = $displayorder;
            $datas['parents'][$category['id']]['level'] = '1';

            if ($category['children'] && is_array($category['children'])) {
                $displayorder_child = count($category['children']);
                foreach ($category['children'] as $child) {
                    $datas['cateids'][] = $child['id'];
                    $datas['childrens'][$child['id']]['id'] = $child['id'];
                    $datas['childrens'][$child['id']]['parent_id'] = $category['id'];
                    $datas['childrens'][$child['id']]['display_order'] = $displayorder_child;
                    $datas['childrens'][$child['id']]['level'] = '2';

                    if ($child['children'] && is_array($child['children'])) {
                        $displayorder_third = count($child['children']);
                        foreach ($child['children'] as $third) {
                            $datas['cateids'][] = $third['id'];
                            $datas['thirds'][$third['id']]['id'] = $third['id'];
                            $datas['thirds'][$third['id']]['parent_id'] = $child['id'];
                            $datas['thirds'][$third['id']]['display_order'] = $displayorder_third;
                            $datas['thirds'][$third['id']]['level'] = '3';
                            $displayorder_third--;
                        }
                    }
                    $displayorder_child--;
                }
            }
            $displayorder--;
        }
        return $datas;

    }
}