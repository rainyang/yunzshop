<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/7/13
 * Time: 上午16:45
 */

namespace app\backend\modules\goods\models;


use app\common\models\goods\GoodsCoupon;

class Coupon extends GoodsCoupon
{
    public $timestamps = false;

    public static function relationSave($goodsId, $data, $operate)
    {
        if (!$goodsId) {
            return false;
        }
        if (!$data) {
            return false;
        }
        $coupnModel = self::getModel($goodsId, $operate);
        //判断deleted
        if ($operate == 'deleted') {
            return $coupnModel->delete();
        }
        $data['goods_id'] = $goodsId;
        $data['point'] = trim($data['point']);
        $coupnModel->setRawAttributes($data);
        return $coupnModel->save();
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
}