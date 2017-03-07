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
    public static function getList($pageSize)
    {
        return self::uniacid()
            ->paginate($pageSize)
            ->toArray();
    }
    public static function getAll()
    {
        return self::getDispatchList();
    }

    public static function relationSave($goodsId, $data, $operate)
    {
        if(!$goodsId){
            return false;
        }
        $dispatchModel = self::getModel($goodsId, $operate);
        //判断deleted
        if ($operate == 'deleted') {
            return $dispatchModel->delete();
        }
        $data['goods_id'] = $goodsId;
        $dispatchModel->setRawAttributes($data);
        return $dispatchModel->save();
    }

    public static function getModel($goodsId,$operate)
    {
        $model = false;
        if($operate != 'created') {
            $model = static::where(['goods_id' => $goodsId])->first();
        }
        !$model && $model =  new static;

        return $model;
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