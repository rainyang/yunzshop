<?php
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/2/22
 * Time: 下午2:24
 */
namespace app\backend\modules\goods\models;

use Illuminate\Database\Eloquent\Model;

class CategoryModel extends Model
{
    public $table = 'yz_goods_category';
    
    public static function getCategorys()
    {
        $data = ['111', '2222'];
        return $data;
    }
}