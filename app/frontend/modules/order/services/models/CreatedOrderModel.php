<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/21
 * Time: 上午9:31
 */

namespace app\frontend\modules\order\services\models;


use app\frontend\modules\discount\services\DiscountService;
use app\frontend\modules\dispatch\services\DispatchService;

class CreatedOrderModel extends OrderModel
{
    private $order;
    protected $orderGoods = [];

    public function __construct($order, $orderGoodsModels)
    {
        $this->order = $order;
        parent::__construct($orderGoodsModels);
    }

    public function setOrderGoods(array $orderGoods)
    {
        $this->orderGoods = $orderGoods;

    }


}