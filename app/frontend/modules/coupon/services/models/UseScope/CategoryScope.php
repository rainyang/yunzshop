<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/28
 * Time: 下午2:44
 */

namespace app\frontend\modules\coupon\services\models\UseScope;


use app\common\exceptions\AppException;
use app\frontend\modules\goods\services\models\PreGeneratedOrderGoodsModel;

class CategoryScope extends CouponUseScope
{
    protected function _getOrderGoodsOfUsedCoupon()
    {
        $orderGoods = $this->coupon->getPreGeneratedOrderModel()->getOrderGoodsModels()->filter(
            function ($orderGoods) {
                /**
                 * @var $orderGoods PreGeneratedOrderGoodsModel
                 */

                return !collect($this->coupon->getMemberCoupon()->belongsToCoupon->category_ids)->intersect(
                    $orderGoods->goods->belongsToCategorys->pluck('category_id'))->isEmpty();
            });

        if ($orderGoods->unique('is_plugin')->count() > 1) {
            throw new AppException('自营商品与第三方商品不能共用一张优惠券');
        }

        return $orderGoods;
    }

}