<?php
namespace app\frontend\modules\order\model;

use app\common\models\Order;
use app\frontend\modules\member\model\MemberModel;
use app\frontend\modules\order\service\OrderService;
use app\frontend\modules\shop\model\ShopModel;

class PreGeneratedOrderModel extends OrderModel
{

    private $_pre_order_goods_models;

    private $_has_calculated;

    public function __construct(array $pre_order_goods_models = null)
    {
        if (isset($pre_order_goods_models)) {
            $this->_pre_order_goods_models = $pre_order_goods_models;
        }
        $this->_has_calculated = false;
    }

    public function addPreGeneratedOrderGoods(array $pre_order_goods_models)
    {

        $this->_pre_order_goods_models = array_merge($this->_pre_order_goods_models, $pre_order_goods_models);
        $this->_has_calculated = false;

    }

    public function setMemberModel(MemberModel $member_model)
    {
        $this->member_model = $member_model;
    }

    public function setShopModel(ShopModel $shop_model)
    {
        $this->shop_model = $shop_model;
    }
    //对插件观察者 开放的接口
    public function setPrice($price){
        $this->price -= $price;
        //log();
    }
    private function calculate()
    {
        $this->_has_calculated = true;
        $this->total = $this->calculateTotal();
        $this->price = $this->calculatePrice();
        $this->goods_price = $this->calculateGoodsPrice();

    }

    private function calculateTotal()
    {
        $result = 0;
        foreach ($this->_pre_order_goods_models as $pre_order_goods_model) {
            $result += $pre_order_goods_model->total;
        }
        return $result;
    }

    private function calculatePrice()
    {
        $result = 0;
        foreach ($this->_pre_order_goods_models as $pre_order_goods_model) {
            $result += $pre_order_goods_model->price;
        }
        return $result;
    }

    private function calculateGoodsPrice()
    {
        $result = 0;
        foreach ($this->_pre_order_goods_models as $pre_order_goods_model) {
            $result += $pre_order_goods_model->goods_price;
        }
        return $result;
    }


    public function __get($name)
    {
        if ($this->_has_calculated == false) {
            $this->calculate();
        }
        if (isset($this->$name)) {
            return $this->$name;
        }

        return null;
    }
    public function show(){
        if ($this->_has_calculated == false) {
            $this->calculate();
        }
        return $this;
    }
    public function generate()
    {
        if ($this->_has_calculated == false) {
            $this->calculate();
        }
        $this->createOrder();
        $this->createOrderGoods();
        return true;
    }

    private function createOrderGoods(){
        foreach ($this->_pre_order_goods_models as $pre_order_goods_model){
            $pre_order_goods_model->generate($this);
        }
    }
    private function createOrder()
    {
        $data = array(
            'uniacid' => $this->shop_model->uniacid,
            'member_id' => $this->member_model->uid,
            'order_sn' => OrderService::createOrderSN(),
            'order_price' => $this->price,
            'goods_price' => $this->goods_price,
            'create_time' => time(),
        );
        echo '订单插入的数据为:';
        $this->id = 1;
         var_dump($data);

        return Order::insert($data);
    }

}