<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
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
            ->first();
    }


    public static function relationSave($goodsId, $data, $operate)
    {
        if (!$goodsId) {
            return false;
        }
        if (!$data) {
            return false;
        }
        $saleModel = self::getModel($goodsId, $operate);
        //判断deleted
        if ($operate == 'deleted') {
            return $saleModel->delete();
        }
        $data['goods_id'] = $goodsId;
        $data['ed_full'] = empty($data['ed_full']) ? 0 : $data['ed_full'];
        $data['ed_reduction'] = empty($data['ed_reduction']) ? 0 : $data['ed_reduction'];
        $data['point'] = trim($data['point']);
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
}