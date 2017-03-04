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
    public $timestamps = false;

    /**
     * 获取配送模板所有数据
     * @param int $goodsId
     * @return array
     */
    public static function getList( $pageSize)
    {
        return self::uniacid()
            ->paginate($pageSize)
            ->toArray();
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
    public static function updatedDispatch($goodsId, $DispatchInfo)
    {
        return self::where('goods_id', $goodsId)->update($DispatchInfo);
    }

    /**
     * 配送模板数据删除
     * @param int $goodsId
     * @return mixed
     */
    public static function deletedShare($goodsId)
    {
        return self::where('goods_id', $goodsId)->delete();
    }

}