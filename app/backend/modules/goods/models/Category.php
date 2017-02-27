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
    public $timestamps = false;
    
    public static function saveAddCategory($category)
    {
        return self::insert($category);
    }

    /**
     * @param $category
     * @param $id
     * @return mixed
     */
    public static function saveEditCategory($category, $id)
    {
        return self::where('id', $id)
            ->update($category);
    }

    /**
     * @param $id
     * @return \Illuminate\Database\Eloquent\Model|null|static
     */
    public static function getCategory($id)
    {
        return self::where('id', $id)
            ->first();
    }

    /**
     * @param $id
     * @return mixed
     */
    public static function daletedCategory($id)
    {
        return self::where('id', $id)
            ->orWhere('parent_id', $id)
            ->delete();
    }
    
    public static function parentCategory()
    {
        
    }
}