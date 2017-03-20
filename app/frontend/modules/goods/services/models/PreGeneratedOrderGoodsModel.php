<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/28
 * Time: 下午1:44
 */

namespace app\frontend\modules\goods\services\models;

use app\common\models\Goods;
use app\common\models\OrderGoods;

use app\common\ServiceModel\ServiceModel;
use app\frontend\modules\discount\services\DiscountService;
use app\frontend\modules\dispatch\services\DispatchService;
use app\frontend\modules\dispatch\services\models\GoodsDispatch;
use app\frontend\modules\order\services\models\PreGeneratedOrderModel;


class PreGeneratedOrderGoodsModel extends ServiceModel
{
    private $total;
    private $order_model;
    private $goods_model;
    private $price;
    private $goods_price;
    private $_has_calculated;
    private $dispatch_details = [];
    private $discount_details = [];
    private $GoodsDispatch;
    private $GoodsDiscount;

    public function __construct(Goods $goods_model, $total = 1)
    {
        $this->goods_model = $goods_model;
        $this->total = $total;
        $this->GoodsDispatch =DispatchService::getPreOrderGoodsDispatchModel($this);
        $this->GoodsDiscount =DiscountService::getPreOrderGoodsDiscountModel($this);

        $this->dispatch_details = $this->getDispatchDetails();
        $this->_has_calculated = false;

    }

    //为监听者提供的方法,设置优惠详情
    public function setDiscountDetails($discount_details)
    {
        $this->discount_details = $discount_details;
    }
    //设置商品数量
    public function setTotal($total)
    {

        $this->total = $total;
        $this->_has_calculated = false;

    }
    //为订单model提供的方法 ,设置所属的订单model
    public function setOrderModel(PreGeneratedOrderModel $order_model)
    {
        $this->order_model = $order_model;

    }
    //统计商品数据
    private function calculate()
    {
        $this->_has_calculated = true;
        $this->goods_price = $this->calculateGoodsPrice();
        $this->price = $this->calculatePrice();

    }
    //计算最终价格
    private function calculatePrice()
    {
        //最终价格=商品价格-优惠价格
        $result = $this->calculateGoodsPrice() - $this->calculateDiscountPrice();
        return $result;
    }
    //计算商品价格
    private function calculateGoodsPrice()
    {
        return $this->total * $this->goods_model->price;
    }
    //计算商品优惠价格
    private function calculateDiscountPrice()
    {
        return $this->GoodsDiscount->getDiscountPrice();

    }
    //获取订单商品配送详情
    private function getDispatchDetails(){
        return $this->GoodsDispatch->getDispatchDetails();
    }
    //获取订单优惠详情
    private function getDiscountDetails(){
        return $this->GoodsDiscount->getDiscountDetails();
    }
    //显示商品数据
    public function toArray()
    {

        return $data = array(
            'goods_id' => $this->goods_model->id,
            'goods_sn' => $this->goods_model->goods_sn,
            'price' => $this->price,
            'total' => $this->total,
            'title' => $this->goods_model->title,
            'thumb' => $this->goods_model->thumb,
            'discount_details' => $this->getDiscountDetails(),
            'dispatch_details' => $this->getDispatchDetails(),

        );
        return $data;
    }
    public function getTotal(){
        return $this->total;

    }
    public function getPrice(){
        return $this->price;
    }
    //订单商品插入数据库
    public function generate(PreGeneratedOrderModel $order_model = null)
    {
        if (isset($order_model)) {
            $this->setOrderModel($order_model);
        }

        $data = array(
            'uniacid' => $this->order_model->shop_model->uniacid,
            'order_id' => $this->order_model->id,
            'goods_id' => $this->goods_model->id,
            'goods_sn' => $this->goods_model->goods_sn,
            'uid' => $this->order_model->member_model->uid,
            'goods_price' => $this->goods_price,
            'price' => $this->price,
            'total' => $this->total,
            'title' => $this->goods_model->title,
            'thumb' => $this->goods_model->thumb,
            'discount_details' => $this->discount_details,
            'dispatch_details' => $this->dispatch_details,
        );
        echo '订单商品插入数据为';
        var_dump($data);
        //return;
        return OrderGoods::create($data);
    }
    //外部获得订单属性前,先统计商品数据
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
}