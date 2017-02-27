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
     * @param $uniacid
     * @return mixed
     */
    public static function getBrandTotal($uniacid)
    {
        return self::where('uniacid', $uniacid)
            ->count();
    }

    /**
     * @param $uniacid
     * @param $pindex
     * @param $psize
     * @return mixed
     */
    public static function getBrands($uniacid, $pindex, $psize)
    {
        return self::where('uniacid', $uniacid)
            ->skip(($pindex - 1) * $psize)
            ->take($psize)
            ->get()
            ->toArray();
    }

    /**
     * @param $brand
     * @return mixed
     */
    public static function saveAddBrand($brand)
    {
        return self::insert($brand);
    }

    /**
     * @param $id
     * @return mixed
     */
    public static function getBrand($id)
    {
        return self::where('id', $id)
            ->first()
            ->toArray();
    }

    /**
     * @param $brand
     * @param $id
     * @return mixed
     */
    public static function saveEditBrand($brand, $id)
    {
        return self::where('id', $id)
            ->update($brand);
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