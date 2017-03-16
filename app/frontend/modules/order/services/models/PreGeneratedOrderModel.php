<?php
namespace app\frontend\modules\order\services\models;

use app\common\events\order\OrderCreatedEvent;
use app\common\models\Order;
use app\common\models\Member;

use app\common\ServiceModel\ServiceModel;
use app\frontend\modules\order\services\OrderService;
use app\frontend\modules\shop\services\models\ShopModel;
use Illuminate\Support\Facades\Event;

class PreGeneratedOrderModel extends ServiceModel
{
    private $id;
    private $total;
    private $price;
    private $goods_price;
    //订单运费价格
    private $dispatch_price = 0;
    //优惠详情
    private $discount_details = [];
    //商城model实例
    private $shop_model;
    //用户model实例
    private $member_model;
    //运费类实例
    private $order_dispatch_obj;
    //优惠类实例
    private $order_discount_obj;
    //未插入数据库的订单商品数组
    private $_pre_order_goods_models = [];
    //订单价格没有被计算过
    private $_has_calculated;

    //记录添加的商品
    public function __construct(array $pre_order_goods_models = null)
    {
        //dd($pre_order_goods_models);exit;
        if (isset($pre_order_goods_models)) {
            $this->addPreGeneratedOrderGoods($pre_order_goods_models);
        }
    }
    //对外提供的获取订单商品方法
    public function getOrderGoodsModels()
    {
        return $this->_pre_order_goods_models;
    }
    //添加订单商品
    private function addPreGeneratedOrderGoods(array $pre_order_goods_models)
    {
        $this->_pre_order_goods_models = array_merge($this->_pre_order_goods_models, $pre_order_goods_models);
        $this->_has_calculated = false;
    }
    //为优惠类提供的 设置优惠详情方法
    public function setDiscountDetails($discount_details)
    {
        $this->discount_details = $discount_details;
    }

    //设置订单所属用户
    public function setMemberModel(Member $member_model)
    {
        $this->member_model = $member_model;
    }
    //设置订单所属店铺

    public function setShopModel(ShopModel $shop_model)
    {
        $this->shop_model = $shop_model;
    }

    //统计订单数据
    private function _calculate()
    {
        $this->_has_calculated = true;
        $this->total = $this->calculateTotal();
        $this->goods_price = $this->calculateGoodsPrice();

        $this->dispatch_price = $this->calculateDispatchPrice();

        $this->price = $this->calculatePrice();

    }

    //统计商品总数
    private function calculateTotal()
    {
        $result = 0;
        foreach ($this->_pre_order_goods_models as $pre_order_goods_model) {
            $result += $pre_order_goods_model->total;
        }
        return $result;
    }
    //计算订单优惠
    private function calculateDiscountPrice(){
        $this->order_discount_obj = new OrderDiscount($this);
        return $this->order_discount_obj->getDiscountPrice();
    }
    //计算订单运费
    private function calculateDispatchPrice(){
        $this->order_dispatch_obj = new OrderDispatch($this);
        return $this->order_dispatch_obj->getDispatchPrice();
    }

    //计算订单最终价格
    private function calculatePrice()
    {
        //订单最终价格 = 商品最终价格 - 订单优惠 - 订单运费
        return $this->calculateGoodsPrice() - $this->calculateDiscountPrice() + $this->calculateDispatchPrice();
    }
    //统计订单商品最终价格
    private function calculateGoodsPrice()
    {
        $result = 0;
        foreach ($this->_pre_order_goods_models as $pre_order_goods_model) {
            $result += $pre_order_goods_model->price;
        }
        return $result;
    }

    //外部获取订单属性时 先进行计算
    public function __get($name)
    {
        if ($this->_has_calculated == false) {
            $this->_calculate();
        }
        if (isset($this->$name)) {
            return $this->$name;
        }

        return null;
    }
    //订单显示
    public function toArray()
    {
        if ($this->_has_calculated == false) {
            $this->_calculate();
        }
        $data = array(
            'price' => $this->price,
            'goods_price' => $this->goods_price,
            'dispatch_price' => $this->dispatch_price,
            'dispatch_types' => $this->dispatch_types,
        );
        foreach ($this->_pre_order_goods_models as $order_goods_model) {
            $data['order_goods'][] = $order_goods_model->toArray();
        }
        return $data;
    }
    //订单生成
    public function generate()
    {
        if ($this->_has_calculated == false) {
            $this->_calculate();
        }
        $order_model = $this->createOrder();
        $this->id = $order_model->id;
        $this->createOrderGoods();
        Event::fire(new OrderCreatedEvent($order_model));
        //配送类记录订单配送信息
        $this->order_dispatch_obj->saveDispatchDetail($order_model);
        //优惠类记录订单配送信息
        $this->order_discount_obj->saveDiscountDetail($order_model);

        return true;
    }
    //订单商品生成
    private function createOrderGoods()
    {
        foreach ($this->_pre_order_goods_models as $pre_order_goods_model) {
            $pre_order_goods_model->generate($this);
        }
    }
    //订单插入数据库
    private function createOrder()
    {
        $data = array(
            'uniacid' => $this->shop_model->uniacid,
            'order_sn' => OrderService::createOrderSN(),
            'member_id' => $this->member_model->uid,
            'price' => $this->price,
            'goods_price' => $this->goods_price,
            'create_time' => time(),
            'discount_details' => $this->discount_details,
        );
        echo '订单插入的数据为:';
        var_dump($data);

        return Order::create($data);;
    }

}