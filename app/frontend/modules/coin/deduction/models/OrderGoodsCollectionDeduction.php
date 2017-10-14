<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/6
 * Time: 上午10:12
 */

namespace app\frontend\modules\coin\deduction\models;

use app\common\models\VirtualCoin;
use app\frontend\modules\coin\deduction\orderGoods\PreOrderGoodsDeduction;
use app\frontend\modules\orderGoods\models\PreOrderGoodsCollection;

class OrderGoodsCollectionDeduction
{
    /**
     * @var PreOrderGoodsCollection
     */
    protected $orderGoodsCollection;

    function __construct(PreOrderGoodsCollection $orderGoodsCollection){
        $this->orderGoodsCollection = $orderGoodsCollection;
    }

    /**
     * @return VirtualCoin
     */
    /**
     * @return VirtualCoin
     */
    public function getUsablePoint()
    {
        return $this->orderGoodsCollection->reduce(function ($result, $aOrderGoods) {
            /**
             * @var PreOrderGoodsDeduction $aOrderGoods
             */
            if(!isset($result)){
                return $aOrderGoods->getUsableCoin();
            }
            return $aOrderGoods->getUsableCoin()->plus($result);
        });

    }
}