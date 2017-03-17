<?php
/**
 * 配送模板数据操作
 * Created by PhpStorm.
 * User: luckystar_D
 * Date: 2017/2/24
 * Time: 下午2:31
 */

namespace app\backend\modules\goods\models;


class GoodsDispatch extends \app\common\models\goods\GoodsDispatch
{
    public $timestamps = false;

    /**
     * 获取商品配送信息关联数据
     * @param int $goodsId
     * @return array
     */
    public static function getInfo($goodsId)
    {
        return self::getDispatchInfo($goodsId);
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
    public static function relationValidator($goodsId, $data, $operate)
    {
        if ($data) {
            return parent::validator($data);
        }
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
     * 商品配送信息关联数据添加
     * @param array $DispatchInfo
     * @return bool
     */
    public static function createdDispatch($DispatchInfo)
    {
        return self::insert($DispatchInfo);
    }

    /**
     * 商品配送信息关联数据更新
     * @param array $DispatchInfo
     * @return mixed
     */
    public static function updatedDispatch($dispatchId, $DispatchInfo)
    {
        return self::where('id', $dispatchId)->update($DispatchInfo);
    }

    /**
     * 商品配送信息关联数据删除
     * @param int $goodsId
     * @return mixed
     */
    public static function deletedDispatch($dispatchId)
    {
        return self::where('id', $dispatchId)->delete();
    }

}