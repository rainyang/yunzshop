<?php
namespace app\backend\modules\goods\models;
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/2/27
 * Time: 上午9:18
 */

class Brand extends \app\common\models\Brand
{
    public $timestamps = false;

    /**
     * @param $pageSize
     * @return mixed
     */
    public static function getBrands( $pageSize)
    {
        return self::uniacid()
            ->paginate($pageSize)
            ->toArray();
    }


    /**
     * @param $id
     * @return mixed
     */
    public static function getBrand($id)
    {
        return self::where('id', $id)
            ->first();
    }


    /**
     * @param $id
     * @return mixed
     */
    public static function daletedBrand($id)
    {
        return self::where('id', $id)
            ->delete();
    }

    /**
     *  定义字段名
     * 可使
     * @return array */
    public static function atributeNames() {
        return [
            'name'=> '品牌名称',
        ];
    }
    
    /**
     * 字段规则
     * @return array */
    public static function rules() {
        return [
            'name' => 'required',
        ];
    }
}