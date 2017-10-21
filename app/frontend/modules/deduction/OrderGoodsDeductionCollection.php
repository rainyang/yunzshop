<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/6
 * Time: 上午10:12
 */

namespace app\frontend\modules\deduction;

use app\common\models\VirtualCoin;
use app\frontend\modules\coin\InvalidVirtualCoin;
use app\frontend\modules\deduction\orderGoods\PreOrderGoodsDeduction;
use Illuminate\Database\Eloquent\Collection;

class OrderGoodsDeductionCollection extends Collection
{

    /**
     * @return VirtualCoin
     */
    public function getUsablePoint()
    {
        $result =  $this->reduce(function ($result, $orderGoodsDeduction) {
            /**
             * @var PreOrderGoodsDeduction $orderGoodsDeduction
             */

            if(!isset($result)){
                return $orderGoodsDeduction->getUsableCoin();
            }
            return $orderGoodsDeduction->getUsableCoin()->plus($result);
        });
        return $result;
    }

    /**
     * 订单商品抵扣集合中 已使用的积分
     * @return VirtualCoin
     */
    public function getUsedPoint()
    {
        $result = $this->reduce(function ($result, $orderGoodsDeduction) {
            /**
             * @var PreOrderGoodsDeduction $orderGoodsDeduction
             */
            if(!$orderGoodsDeduction->used()){
                // 没用过 0
                return $result;
            }
            return $result->plus($orderGoodsDeduction->getUsableCoin());

        },new InvalidVirtualCoin());

        return $result?:new InvalidVirtualCoin();
    }
}