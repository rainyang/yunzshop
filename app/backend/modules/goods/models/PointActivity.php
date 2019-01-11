<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/11
 * Time: 16:46
 */

namespace app\backend\modules\goods\models;



use app\common\models\goods\GoodsPointActivity;
use app\common\traits\MessageTrait;

class PointActivity extends GoodsPointActivity
{
    use MessageTrait;

    public static function relationSave($goodsId, $data, $operate)
    {
//        dd($data);
        if (!$goodsId) {
            return false;
        }
        if (!$data) {
            return false;
        }
        $saleModel = self::getModel($goodsId, $operate);

        if ($operate == 'deleted') {
            return $saleModel->delete();
        }

        $data['goods_id'] = $goodsId;
        $data['uniacid'] = \YunShop::app()->uniacid;
        $data['status'] = $data['status']?:0;

        $saleModel->setRawAttributes($data);

        return $saleModel->save();
    }

    public static function getModel($goodsId, $operate)
    {
        $model = false;
        if ($operate != 'created') {
            $model = static::where(['goods_id' => $goodsId])->first();
        }
        !$model && $model = new static;

        return $model;
    }

    public static function relationValidator($goodsId, $data, $operate)
    {
//        dd($data);
        $flag = false;
        $model = new static;
        $validator = $model->validator($data);
        if($validator->fails()){
            $model->error($validator->messages());
        }else{
            $flag = true;
        }
        return $flag;
    }
}