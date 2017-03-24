<?php
namespace app\frontend\modules\order\services\models;

use app\common\events\order\AfterOrderCreatedEvent;
use app\common\models\Order;
use app\common\models\Member;

use app\frontend\modules\discount\services\DiscountService;
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
class PreGeneratedOrderModel extends OrderModel
{
    protected $id;
    /**
     * @var 商城model实例
     */
    protected $shop_model;
    /**
     * @var用户model实例
     */
    protected $member_model;



    /**
     * 记录添加的商品
     * PreGeneratedOrderModel constructor.
     * @param array|null $OrderGoodsModels
     */
    public function __construct(array $OrderGoodsModels = null)
    {
        if (!isset($OrderGoodsModels)) {
            echo '订单商品为空!';exit;
        }
        parent::__construct($OrderGoodsModels);
    }
    protected function setOrderGoodsModels(array $OrderGoodsModels)
    {
        $this->_OrderGoodsModels = $OrderGoodsModels;
    }

    protected function setDiscount()
    {
        $this->_OrderDiscount = DiscountService::getPreOrderDiscountModel($this);
    }
    protected function setDispatch()
    {
        $this->_OrderDispatch = DispatchService::getPreOrderDispatchModel($this);
    }

    /**
     * 对外提供的获取订单商品方法
     * @return array
     */
    public function getOrderGoodsModels()
    {
        return $this->_OrderGoodsModels;
    }

    /**
     * 添加订单商品
     * @param array $pre_order_goods_models
     */
    private function addPreGeneratedOrderGoods(array $pre_order_goods_models)
    {
        $this->_OrderGoodsModels = array_merge($this->_OrderGoodsModels, $pre_order_goods_models);
    }

    /**
     * 设置订单所属用户
     * @param Member $member_model
     */
    public function setMemberModel(Member $member_model)
    {
        $this->member_model = $member_model;
    }

    /**
     * 设置订单所属店铺
     * @param ShopModel $shop_model
     */

    public function setShopModel(ShopModel $shop_model)
    {
        $this->shop_model = $shop_model;
    }
    public function getShopModel(){
        return $this->shop_model;
    }
    public function getMemberModel(){
        return $this->member_model;
    }

    /**
     * 输出订单信息
     * @return array
     */
    public function toArray()
    {
        $data = array(
            'price' => $this->getPrice(),
            'goods_price' => $this->getGoodsPrice(),
            'dispatch_price' => $this->getDispatchPrice(),
            'dispatch_types' => $this->_OrderDispatch->getDispatchTypeId(),
        );
        foreach ($this->_OrderGoodsModels as $order_goods_model) {
            $data['order_goods'][] = $order_goods_model->toArray();
        }
        return $data;
    }

    /**
     * @return bool 订单插入数据库,触发订单生成事件
     */
    public function generate()
    {
        $order_model = $this->createOrder();
        $this->id = $order_model->id;
        $this->createOrderGoods();
        event(new AfterOrderCreatedEvent($order_model));
        return true;
    }

    /**
     * 订单商品生成
     */
    private function createOrderGoods()
    {
        foreach ($this->_OrderGoodsModels as $pre_order_goods_model) {
            $pre_order_goods_model->generate($this);
        }
    }

    /**
     * 订单插入数据库
     * @return static 新生成的order model
     */
    private function createOrder()
    {
        $data = array(
            'uniacid' => $this->shop_model->uniacid,
            'order_sn' => OrderService::createOrderSN(),
            'goods_total'=> $this->getGoodsTotal(),
            'uid' => $this->member_model->uid,
            'price' => $this->getPrice(),
            'goods_price' => $this->getGoodsPrice(),
            'create_time' => time(),
            //配送类获取订单配送信息
            'dispatch_details'=>$this->_OrderDispatch->getDispatchDetails(),
            //优惠类记录订单配送信息
            'discount_details' => $this->_OrderDiscount->getDiscountDetails(),
            //配送类获取订单配送方式id
            'dispatch_type_id'=>$this->_OrderDispatch->getDispatchTypeId(),
            'dispatch_price' => $this->_OrderDispatch->getDispatchPrice(),
            'discount_price' => $this->getDiscountPrice()
        );
        //todo 测试
        echo '订单插入的数据为:';
        var_dump($data);

        return Order::create($data);
    }

}