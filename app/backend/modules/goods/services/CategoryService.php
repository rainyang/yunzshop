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
    public static function getLists($category)
    {
        foreach ($category as $row) {
            if( empty($row->parent_id) ){
                $data[parents][] = $row;
            } else {
                $data[childrens][$row['parent_id']][] = $row;
            }
        }
        return $data;
    }
    
    public static function processCategory($categorys)
    {
        if(!empty($categorys)) {
            ca('shop.category.edit');
            $categorys = json_decode(html_entity_decode($categorys), true);
            if (!is_array($categorys)) {
                message('分类保存失败，请重试!', '', 'error');
            }
            $displayorder = count($categorys);
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
        message('分类保存失败，请重试!', '', 'error');
    }
}