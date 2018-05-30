<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/5/30
 * Time: 下午1:49
 */

namespace app\frontend\modules\orderGoods\price;

use app\frontend\modules\orderGoods\models\PreOrderGoods;

class OrderGoodsDiscount
{
    /**
     * @var PreOrderGoods 
     */
    private $orderGoods;
    public function __construct(PreOrderGoods $orderGoods)
    {
        $this->orderGoods = $orderGoods;
    }
}