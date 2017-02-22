<?php
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/2/22
 * Time: 下午2:53
 */
namespace app\backend\modules\goods\services;

use app\backend\modules\goods\models\CategoryModel;

class CategoryService
{

    public static function getLists()
    {
        $list = CategoryModel::getCategorys();
        return $list;
    }
}