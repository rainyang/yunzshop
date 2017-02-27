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
            ->toArray();;
    }

}