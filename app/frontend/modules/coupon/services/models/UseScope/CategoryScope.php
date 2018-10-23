<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/28
 * Time: 下午2:44
 */

namespace app\frontend\modules\coupon\services\models\UseScope;


use app\common\exceptions\AppException;
use app\frontend\modules\orderGoods\models\PreOrderGoods;

class CategoryScope extends CouponUseScope
{
    protected function _getOrderGoodsOfUsedCoupon()
    {
        $orderGoods = $this->coupon->getPreOrder()->orderGoods->filter(
            function ($orderGoods) {
                /**
                 * @var $orderGoods PreOrderGoods
                 */
                //订单商品所属的所有分类id
                $orderGoodsCategoryIds = explode(',',data_get($orderGoods->belongsToGood->belongsToCategorys->first(),'category_ids',''));

                //优惠券的分类id数组 与 订单商品的所属分类 的分类数组 有交集
                return collect($this->coupon->getMemberCoupon()->belongsToCoupon->category_ids)
                    ->intersect($orderGoodsCategoryIds)->isNotEmpty();
            });

        if ($orderGoods->unique('is_plugin')->count() > 1) {
            throw new AppException('自营商品与第三方商品不能共用一张优惠券');
        }

        return $orderGoods;
    }

}