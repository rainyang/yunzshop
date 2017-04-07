<?php
namespace app\frontend\modules\coupon\services\models\UseScope;

use app\frontend\modules\goods\services\models\PreGeneratedOrderGoodsModel;
use app\frontend\modules\goods\services\models\PreGeneratedOrderGoodsModelGroup;

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
    protected function getOrderGoodsOfUsedCoupon(){
        if(isset($this->orderGoods)){
            return $this->orderGoods;
        }
        return $this->orderGoods = $this->_getOrderGoodsOfUsedCoupon();
    }
    private function _getOrderGoodsOfUsedCoupon()
    {
        $result = [];
        foreach ($this->coupon->getPreGeneratedOrderModel()->getOrderGoodsModels() as $orderGoodsModel) {
            /**
             * @var $orderGoodsModel PreGeneratedOrderGoodsModel
             */
            if (in_array($orderGoodsModel->getGoodsId(), $this->coupon->getMemberCoupon()->belongsToCoupon->goods_ids)) {
                $result[] = $orderGoodsModel;
            }
        }
        //dd($result);exit;
        return $result;
    }
}