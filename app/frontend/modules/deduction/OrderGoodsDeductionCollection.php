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
     * @return VirtualCoin
     */
    public function getUsedPoint()
    {


        $result =  $this->reduce(function ($result, $orderGoodsDeduction) {
            /**
             * @var PreOrderGoodsDeduction $orderGoodsDeduction
             */

            if(!isset($result)){
                return $orderGoodsDeduction->getUsedCoin();
            }

            return $orderGoodsDeduction->getUsableCoin()->plus($result);
        });
        return $result?:new InvalidVirtualCoin();
    }
}