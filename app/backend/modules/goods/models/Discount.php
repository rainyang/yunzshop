<?php
/**
 * 商品折扣关联表数据操作
 * Created by PhpStorm.
 * User: luckystar_D
 * Date: 2017/2/28
 * Time: 上午11:01
 */

namespace app\backend\modules\goods\models;


use app\backend\modules\goods\services\DiscountService;

class Discount extends \app\common\models\goods\Discount
{
    public $timestamps = false;

    /**
     * 获取商品折扣数据
     * @param int $goodsId
     * @return array
     */
    public static function getList($goodsId)
    {
        return self::getGoodsDiscountList($goodsId);
    }

    public static function relationSave($goodsId, $data, $operate)
    {
        if(!$goodsId){
            return false;
        }
        $discountModel = self::getModel($goodsId, $operate);
        $discounts = self::getList($goodsId);
        $request = false;
        $discount_data = [];
        foreach ($data['discount_value'] as $key => $value) {
            $discount_data[] = [
                'level_discount_type' => !empty($data['level_discount_type']) ? $data['level_discount_type'] : '1',
                'discount_method' =>  !empty($data['discount_method']) ? $data['discount_method'] : '1',
                'level_id' => $key,
                'discount_value' => $value ,
                'goods_id' => $goodsId
            ];
        }
        if ($discounts) {
            if ($operate == 'deleted') {
                return self::deletedDiscount($goodsId);
            }
            foreach ($discount_data as $discount) {

                $discount['goods_id'] = $goodsId;
                dd($discount);
                $discountModel->setRawAttributes($discount);
                $request = $discountModel->save();
            }
        }

        foreach ($discount_data as $discount) {

            $discount['goods_id'] = $goodsId;
            dd($discount);
            $discountModel->setRawAttributes($discount);
            $request = $discountModel->save();
        }
        return $request;
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
     * 商品折扣数据添加
     * @param array $DiscountInfo
     * @return bool
     */
    public static function createdDiscount($DiscountInfo)
    {
        return self::insert($DiscountInfo);
    }

    /**
     * 商品折扣数据更新
     * @param array $DiscountInfo
     * @return mixed
     */
    public static function updatedDiscount($goodsId, $DiscountInfo)
    {
        return self::where('goods_id', $goodsId)->update($DiscountInfo);
    }

    /**
     * 商品折扣数据删除
     * @param int $goodsId
     * @return mixed
     */
    public static function deletedDiscount($goodsId)
    {
        return self::where('goods_id', $goodsId)->delete();
    }

    public static function getDetail()
    {
        return self::hasMany('app\backend\modules\goods\models\DiscountDetail', 'goods_id');
    }
}