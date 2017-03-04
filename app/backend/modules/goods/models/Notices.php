<?php
namespace app\backend\modules\goods\models;
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/2/27
 * Time: 上午9:18
 */

class Notices extends \app\common\models\Notices
{
    public $timestamps = false;

    /**
     * @param $goodsId
     * @return \Illuminate\Database\Eloquent\Model|null|static
     */
    public static function getInfo($goodsId)
    {
        return self::where('goods_id', $goodsId)
        ->first();
    }

    /**
     * @param $notices
     * @return mixed
     */
    public static function createdNotices($notices)
    {
        return self::insert($notices);
    }

    /**
     * @param $goodsId
     * @param $notices
     * @return mixed
     */
    public static function updatedNotices($goodsId, $notices)
    {
        return self::where('goods_id', $goodsId)->update($notices);
    }

    /**
     * @param $goodsId
     * @return mixed
     */
    public static function deletedNotices($goodsId)
    {
        return self::where('goods_id', $goodsId)->delete();
    }
}