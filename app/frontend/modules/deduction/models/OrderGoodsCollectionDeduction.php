<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/6
 * Time: 上午10:12
 */

namespace app\frontend\modules\deduction\models;

use app\common\models\VirtualCoin;
use app\frontend\modules\deduction\orderGoods\PreOrderGoodsDeduction;
use app\frontend\modules\orderGoods\models\PreOrderGoodsCollection;

class OrderGoodsCollectionDeduction
{
    /**
     * @var PreOrderGoodsCollection
     */
    protected $orderGoodsDeductionCollection;

    function __construct($orderGoodsDeductionCollection){
        $this->orderGoodsDeductionCollection = $orderGoodsDeductionCollection;
    }

    /**
     * @return VirtualCoin
     */
    /**
     * @return VirtualCoin
     */
    public function getUsablePoint()
    {


        $result =  $this->orderGoodsDeductionCollection->reduce(function ($result, $orderGoodsDeduction) {
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

}