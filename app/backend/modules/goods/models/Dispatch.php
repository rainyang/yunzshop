<?php
/**
 * 配送模板数据操作
 * Created by PhpStorm.
 * User: luckystar_D
 * Date: 2017/2/24
 * Time: 下午2:31
 */

namespace app\backend\modules\goods\models;


class Dispatch extends \app\common\models\goods\Dispatch
{

    /**
     * 获取配送模板所有数据
     * @param int $goodsId
     * @return array
     */
    public static function getList()
    {
        return self::uniacid()
            ->get();
    }
    public static function getAll()
    {
        return self::getDispatchList();
    }

    /**
     * 获取配送模板单条数据
     * @param int $goodsId
     * @return array
     */
    public static function getOne($id)
    {
        return self::where('id', $id)
            ->first();
    }

    /**
     * 配送模板数据添加
     * @param array $DispatchInfo
     * @return bool
     */
    public static function createdDispatch($DispatchInfo)
    {
        return self::insert($DispatchInfo);
    }

    /**
     * 配送模板数据更新
     * @param array $DispatchInfo
     * @return mixed
     */
    public static function updatedDispatch($dispatchId, $DispatchInfo)
    {
        return self::where('id', $dispatchId)->update($DispatchInfo);
    }

    /**
     * 配送模板数据删除
     * @param int $goodsId
     * @return mixed
     */
    public static function deletedDispatch($dispatchId)
    {
        return self::where('id', $dispatchId)->delete();
    }

}