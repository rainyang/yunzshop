<?php

namespace app\backend\modules\goods\models;

/**
* Author: 芸众商城 www.yunzshop.com
* Date: 2018/5/22
*/
class GoodsService extends \app\common\models\goods\GoodsService
{
	 // public function relationValidator($goodsId, $data, $operate)
    // {
    // }

    public static function relationSave($goodsId, $data, $operate = '')
    {
        if (!$goodsId) {
            return false;
        }
        if (!$data) {
        	return false;
        }
        $model = self::getModel($goodsId, $operate);

        //判断deleted
        if ($operate == 'deleted') {
            return $model->delete();
        }
        $attr['goods_id'] = $goodsId;
        $attr['uniacid'] = \YunShop::app()->uniacid;
        $attr['is_automatic'] = $data['is_automatic'];
        if ($data['is_automatic'] == 1) {
        	$attr['on_shelf_time'] = strtotime($data['time']['start']);
        	$attr['lower_shelf_time'] = strtotime($data['time']['end']);
        }
        if(isset($data['is_refund'])) {
        	$attr['is_refund'] = $data['is_refund'];
        };
        $model->setRawAttributes($attr);

        return $model->save();
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