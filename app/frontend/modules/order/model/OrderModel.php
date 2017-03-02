<?php
namespace app\frontend\modules\order\model;
use app\common\ServiceModel\ServiceModel;

class OrderModel extends ServiceModel
{
    protected $id;
    protected $total;
    protected $price;
    protected $goods_price;
    protected $member_model;
    protected $shop_model;
    protected $order_goods_models;
    protected $order_sn;

    protected $payBehavior;
    protected $sendBehavior;
    protected $confirmBehavior;
    protected $closeBehavior;

}