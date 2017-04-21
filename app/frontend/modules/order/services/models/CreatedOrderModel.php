<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/21
 * Time: 上午9:31
 */

namespace app\frontend\modules\order\services\models;


use app\frontend\modules\discount\services\DiscountService;
use app\frontend\modules\dispatch\services\DispatchService;

class CreatedOrderModel extends OrderModel
{
    private $order;
    protected $orderGoodsModels = [];

    public function __construct($order, $orderGoodsModels)
    {
        $this->order = $order;
        parent::__construct($orderGoodsModels);
    }

    public function setOrderGoodsModels(array $orderGoodsModels)
    {
        $this->orderGoodsModels = $orderGoodsModels;

    }


}