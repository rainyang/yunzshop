<?php
namespace app\frontend\modules\order\services\models;

use app\common\events\OrderGoodsWasAddedInOrder;
use app\common\events\OrderPriceWasCalculated;
use app\common\models\Order;
use app\common\models\Member;

use app\common\ServiceModel\ServiceModel;
use app\frontend\modules\order\services\OrderService;
use app\frontend\modules\shop\services\models\ShopModel;
use Illuminate\Support\Facades\Event;

class PreGeneratedOrderModel extends ServiceModel
{
    protected $id;
    protected $total;
    protected $price;
    protected $goods_price;
    protected $member_model;
    protected $shop_model;
    protected $order_sn;
    protected $dispatch_price = 0;
    protected $discount_price = 0;
    private $dispatch_details = [];
    protected $discount_details = [];

    private $_pre_order_goods_models = [];

    private $_has_calculated;

    public function __construct(array $pre_order_goods_models = null)
    {
        //dd($pre_order_goods_models);exit;
        if (isset($pre_order_goods_models)) {
            $this->_pre_order_goods_models = $pre_order_goods_models;
        }
        $this->_has_calculated = false;
    }
    //对外提供的获取订单商品方法
    public function getOrderGoodsModels()
    {
        return $this->_pre_order_goods_models;
    }
    //添加订单商品
    public function addPreGeneratedOrderGoods(array $pre_order_goods_models)
    {
        //dd($pre_order_goods_models);exit;

        $this->_pre_order_goods_models = array_merge($this->_pre_order_goods_models, $pre_order_goods_models);
        $this->_has_calculated = false;
    }
    //为观察者提供的 设置优惠详情方法
    public function setDiscountDetails($discount_details)
    {
        $this->discount_details = $discount_details;
    }
    //为监听者提供的方法,设置运费详情
    public function setDispatchDetails($dispatch_detail)
    {
        $this->dispatch_details = $dispatch_detail;
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
        Event::fire(new \app\common\events\OrderDiscountWasCalculated($this));

        $result = array_sum(array_column($this->discount_details,'price'));
        return $result;
    }
    //计算订单运费
    private function calculateDispatchPrice(){
        $order_dispatch_obj = new OrderDispatch($this);
        $this->dispatch_details = $order_dispatch_obj->getDispatchDetails();
        return $order_dispatch_obj->getDispatchPrice();
    }

    //计算订单最终价格
    private function calculatePrice()
    {
        /*echo 1;
        dd($this->calculateDispatchPrice());
        echo 2;*/
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

        );
        //dd($this->order_goods_models);
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
        $this->createOrder();
        $this->createOrderGoods();
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
            'member_id' => $this->member_model->uid,
            'order_sn' => OrderService::createOrderSN(),
            'order_price' => $this->price,
            'goods_price' => $this->goods_price,
            'create_time' => time(),
            'discount_details' => json_encode($this->discount_details),
            'dispatch_details' => json_encode($this->dispatch_details),

        );
        echo '订单插入的数据为:';
        $this->id = 1;
        var_dump($data);
        return;

        return Order::insert($data);
    }

}