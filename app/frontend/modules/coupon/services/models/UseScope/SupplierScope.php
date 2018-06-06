<?php

namespace app\frontend\modules\coupon\services\models\UseScope;

use app\common\exceptions\AppException;
use app\frontend\modules\orderGoods\models\PreOrderGoods;
use app\frontend\modules\orderGoods\models\PreOrderGoodsCollection;
use Illuminate\Support\Collection;

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/28
 * Time: 下午1:53
 */
class SupplierScope extends CouponUseScope
{

    /**
     * 订单中使用了该优惠券的商品组
     * @return Collection
     * @throws AppException
     */
    protected function _getOrderGoodsOfUsedCoupon()
    {
        // 没开启供应商插件
        if(!app('plugins')->isEnabled('supplier')){
            return new PreOrderGoodsCollection();
        }
        $orderGoods = $this->coupon->getPreOrder()->getOrderGoodsModels()->filter(
            function (PreOrderGoods  $orderGoods) {
                if(!$orderGoods->goods->is_plugin){
                    // 不是供应商商品
                    return false;
                }

                // 商品供应商id符合优惠券
                return in_array($orderGoods->goods->supplierGoods->supplier_id,$this->coupon->getMemberCoupon()->belongsToCoupon->supplierids);
            });

        if ($orderGoods->unique('is_plugin')->count() > 1) {
            throw new AppException('自营商品与第三方商品不能共用一张优惠券');
        }

        return $orderGoods;
    }

}