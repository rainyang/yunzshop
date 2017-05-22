<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/28
 * Time: 下午2:44
 */

namespace app\frontend\modules\coupon\services\models\UseScope;


use app\common\exceptions\AppException;
use app\frontend\modules\goods\services\models\PreGeneratedOrderGoodsModel;

class ShopScope extends CouponUseScope
{
    protected function _getOrderGoodsOfUsedCoupon()
    {
        $orderGoods = $this->coupon->getPreGeneratedOrderModel()->getOrderGoodsModels()->filter(
            function ($orderGoods) {
                /**
                 * @var $orderGoods PreGeneratedOrderGoodsModel
                 */
//                dd($orderGoods->goods->is_plugin);
//                exit;
                return !$orderGoods->goods->is_plugin;
            });

//        dd($orderGoods->goods);
//        exit;
        if ($orderGoods->unique('is_plugin')->count() > 1) {
            throw new AppException('自营商品与第三方商品不能共用一张优惠券');
        }

        return $orderGoods;
    }
}