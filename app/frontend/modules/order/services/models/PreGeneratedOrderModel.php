<?php
namespace app\frontend\modules\order\services\models;

use app\common\events\order\OrderCreatedEvent;
use app\common\models\Order;
use app\common\models\Member;

use app\common\ServiceModel\ServiceModel;
use app\frontend\modules\discount\services\models\OrderDiscount;
use app\frontend\modules\dispatch\services\DispatchService;
use app\frontend\modules\order\services\OrderService;
use app\frontend\modules\shop\services\models\ShopModel;

/**
 * 订单生成类
 * 输入
 *  用户model
 *  店铺model
 *  未生成的订单商品(实例)app\frontend\modules\goods\services\models\PreGeneratedOrderGoodsModel
 * 输出
 *  预下单信息
 *  订单表插入结果
 * 执行
 *  订单表操作
 * 事件通知
 *  终止订单生成
 *  订单生成后
 * Class PreGeneratedOrderModel
 * @package app\frontend\modules\order\services\models
 */
class PreGeneratedOrderModel extends ServiceModel
{
    private $id;
    private $goods_total;
    private $price;
    private $goods_price;
    //订单运费价格
    private $dispatch_price = 0;
    //商城model实例
    private $shop_model;
    //用户model实例
    private $member_model;
    //运费类实例
    private $_OrderDispatch;
    //优惠类实例
    private $_OrderDiscount;
    //未插入数据库的订单商品数组
    private $_pre_order_goods_models = [];

    //记录添加的商品
    public function __construct(array $pre_order_goods_models = null)
    {
        //dd($pre_order_goods_models);exit;
        if (isset($pre_order_goods_models)) {
            $this->addPreGeneratedOrderGoods($pre_order_goods_models);
        }
        $this->_OrderDispatch = DispatchService::getPreOrderDispatchModel($this);
        $this->_OrderDiscount = new OrderDiscount($this);

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

    //统计商品总数
    private function getGoodsTotal()
    {
        $result = 0;
        foreach ($this->_pre_order_goods_models as $pre_order_goods_model) {
            $result += $pre_order_goods_model->total;
        }
        return $result;
    }
    //计算订单优惠
    private function getDiscountPrice(){
        return $this->_OrderDiscount->getDiscountPrice();
    }
    //计算订单运费
    private function getDispatchPrice(){
        return $this->_OrderDispatch->getDispatchPrice();
    }

    //计算订单最终价格
    private function getPrice()
    {
        //订单最终价格 = 商品最终价格 - 订单优惠 - 订单运费
        return $this->getGoodsPrice() - $this->getDiscountPrice() + $this->getDispatchPrice();
    }
    //统计订单商品最终价格
    private function getGoodsPrice()
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
        if (isset($this->$name)) {
            return $this->$name;
        }

        return null;
    }
    //订单显示
    public function toArray()
    {
        $data = array(
            'price' => $this->getPrice(),
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
        $order_model = $this->createOrder();
        $this->id = $order_model->id;
        $this->createOrderGoods();
        event(new OrderCreatedEvent($order_model));
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
            'goods_total'=> $this->getGoodsTotal(),
            'member_id' => $this->member_model->uid,
            'price' => $this->getPrice(),
            'goods_price' => $this->getGoodsPrice(),
            'create_time' => time(),
            //配送类获取订单配送信息
            'dispatch_details'=>$this->_OrderDispatch->getDispatchDetails(),
            //优惠类记录订单配送信息
            'discount_details' => $this->_OrderDiscount->getDiscountDetails(),
            //配送类获取订单配送方式id
            'dispatch_type_id'=>$this->_OrderDispatch->getDispatchTypeId()
        );
        //todo 测试
        echo '订单插入的数据为:';
        var_dump($data);

        return Order::create($data);
    }

}