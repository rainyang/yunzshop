<?php
/**
 * 商品折扣关联表数据操作
 * Created by PhpStorm.
 * User: luckystar_D
 * Date: 2017/2/28
 * Time: 上午11:01
 */

namespace app\backend\modules\goods\models;


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