<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/28
 * Time: 下午1:44
 */

namespace app\frontend\modules\goods\model;


use app\common\models\Goods;
use app\common\ServiceModel\ServiceModel;
use app\frontend\modules\order\model\OrderModel;

class PreGeneratedOrderGoodsModel extends ServiceModel
{
    private $total;
    private $order_model;
    private $goods_model;
    private $price;
    private $goods_price;
    private $_has_calculated;

    public function __construct(Goods $goods_model,$total=1)
    {
        $this->goods_model = $goods_model;
        $this->total = $total;
        $this->_has_calculated = false;

    }
    public function setTotal($total){

        $this->total = $total;
        $this->_has_calculated = false;

    }
    public function setOrderModel(OrderModel $order_model){
        $this->order_model = $order_model;

    }
    /*public function setGoodsModel($goods_model){
        $this->goods_model = $goods_model;

        $this->_has_calculated = false;

    }*/
    private function calculate(){
        $this->price = $this->calculatePrice();

        $this->goods_price = $this->calculateGoodsPrice();

    }
    private function calculatePrice()
    {
        return $this->total * $this->goods_model->price;
    }
    private function calculateGoodsPrice()
    {
        return $this->total * $this->goods_model->price;
    }
    public function generate()
    {
        $this->order_model->id;
        $data = array(
            'shop_id' => $this->order_model->shop_model->id,
            'order_id' => $this->order_model->id,
            'goods_id' => $this->goods_model->id,
            'goods_sn' => $this->goods_model->goods_sn,
            'member_id' => $this->order_model->member_model->id(),
            'price' => $this->price,
            'total' => $this->total,

        );
        echo '插入数据为';
        return var_dump($data);
        return Goods::insertGetId($data);
    }
    public function __get($name)
    {
        if($this->_has_calculated==false){
            $this->calculate();
        }
        if(isset($this->$name)){
            return $this->$name;
        }

        return null;
    }
}