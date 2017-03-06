<?php
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/3/6
 * Time: 上午11:45
 */

namespace app\backend\modules\goods\models;


class Sale extends \app\common\models\Sale
{
    public $timestamps = false;

    public static function getList($goodsId)
    {
        return self::where('goods_id', $goodsId)
            ->get();
    }


    public static function relationSave($goodsId, $data, $operate)
    {
        if(!$goodsId){
            return false;
        }
        $saleModel = self::getModel($goodsId, $operate);
        //判断deleted
        if ($operate == 'deleted') {
            return $saleModel->delete();
        }
        $data['goods_id'] = $goodsId;
        $saleModel->setRawAttributes($data);
        return $saleModel->save();
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
}