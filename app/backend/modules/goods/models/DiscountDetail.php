<?php
/**
 * 商品折扣与折扣详情与折扣详情关联表数据操作
 * Created by PhpStorm.
 * User: luckystar_D
 * Date: 2017/2/28
 * Time: 上午11:01
 */

namespace app\backend\modules\goods\models;


class DiscountDetail extends \app\common\models\goods\DiscountDetail
{
    public $timestamps = false;

    /**
     * 获取商品折扣与折扣详情数据
     * @param int $goodsId
     * @return array
     */
    public static function getList($discountId)
    {
        return self::getDiscountDetailList($discountId);
    }

    /**
     * 商品折扣与折扣详情数据添加
     * @param array $DiscountInfo
     * @return bool
     */
    public static function createdDetail($DetailInfo)
    {
        return self::insert($DetailInfo);
    }

    /**
     * 商品折扣与折扣详情数据更新
     * @param array $DiscountInfo
     * @return mixed
     */
    public static function updatedDetail($discountId, $DetailInfo)
    {
        return self::where('goods_id', $discountId)->update($DetailInfo);
    }

    /**
     * 商品折扣与折扣详情数据删除
     * @param int $goodsId
     * @return mixed
     */
    public static function deletedDetail($discountId)
    {
        return self::where('goods_id', $discountId)->delete();
    }
}