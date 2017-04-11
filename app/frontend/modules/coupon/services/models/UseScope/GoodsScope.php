<?php
namespace app\frontend\modules\coupon\services\models\UseScope;

use app\common\exceptions\AppException;
use app\frontend\modules\goods\services\models\PreGeneratedOrderGoodsModel;
use app\frontend\modules\goods\services\models\PreGeneratedOrderGoodsModelGroup;
use Illuminate\Support\Collection;

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/28
 * Time: 下午1:53
 */
class GoodsScope extends CouponUseScope
{
    protected $orderGoods;
    public function valid()
    {
        if(count($this->getOrderGoodsOfUsedCoupon())){
            $this->setOrderGoodsGroup();
            return true;
        }

        return false;
    }

    /**
     * 将订单商品装入 订单商品组对象
     */
    private function setOrderGoodsGroup()
    {
        $this->orderGoodsGroup = new PreGeneratedOrderGoodsModelGroup($this->getOrderGoodsOfUsedCoupon());
    }

    /**
     * (缓存)订单中使用了该优惠券的商品组
     * @return Collection
     */
    protected function getOrderGoodsOfUsedCoupon(){
        if(isset($this->orderGoods)){
            return $this->orderGoods;
        }
        return $this->orderGoods = $this->_getOrderGoodsOfUsedCoupon();
    }

    /**
     * 订单中使用了该优惠券的商品组
     * @return Collection
     * @throws AppException
     */
    private function _getOrderGoodsOfUsedCoupon()
    {
        $orderGoods =  $this->coupon->getPreGeneratedOrderModel()->getOrderGoodsModels()->filter(
            function ($orderGoods){
                /**
                 * @var $orderGoods PreGeneratedOrderGoodsModel
                 */
                return in_array($orderGoods->getGoodsId(), $this->coupon->getMemberCoupon()->belongsToCoupon->goods_ids);
        });
        if($orderGoods->unique('is_plugin')->count() > 1) {
            throw new AppException('自营商品与第三方商品不能共用一张优惠券');
        }
        return $orderGoods;
    }
}