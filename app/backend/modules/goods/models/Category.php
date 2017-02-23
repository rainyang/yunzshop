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

    
    public static function editAllCategorys($data, $uniacid)
    {
        //@todo 事务处理
        foreach ($data as $value) {
            self::where('uniacid', $uniacid)
                ->where('id', $value['id'])
                ->update(['parent_id' => $value['parent_id'], 'display_order' => $value['display_order'], 'level' => $value['level']]);
        }
    }

    public static function delCategorys($data, $uniacid)
    {
        $data = self::whereNotIn('id', $data)->delete();
        return $data;
    }
}