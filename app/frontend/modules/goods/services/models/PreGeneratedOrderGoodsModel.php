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
use app\frontend\modules\order\services\model\OrderModel;
use app\frontend\modules\order\services\model\PreGeneratedOrderModel;

class PreGeneratedOrderGoodsModel extends ServiceModel
{
    private $total;
    private $order_model;
    private $goods_model;
    private $price;
    private $goods_price;
    private $_has_calculated;

    public function __construct(Goods $goods_model, $total = 1)
    {
        $this->goods_model = $goods_model;
        $this->total = $total;
        $this->_has_calculated = false;

    }

    public function setTotal($total)
    {

        $this->total = $total;
        $this->_has_calculated = false;

    }

    public function setOrderModel(PreGeneratedOrderModel $order_model)
    {
        $this->order_model = $order_model;

    }

    /*public function setGoodsModel($goods_model){
        $this->goods_model = $goods_model;

        $this->_has_calculated = false;

    }*/
    private function calculate()
    {
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
    public function toArray(){
        return $data = array(
            'goods_id' => $this->goods_model->id,
            'goods_sn' => $this->goods_model->goods_sn,
            'price' => $this->price,
            'total' => $this->total,
            'title' => $this->goods_model->title,
            'thumb' => $this->goods_model->thumb,
        );
        return $data;
    }
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
            'member_id' => $this->order_model->member_model->uid,
            'price' => $this->price,
            'total' => $this->total,
            'title' => $this->goods_model->id,
            'thumb' => $this->goods_model->thumb,

        );
        echo '订单商品插入数据为';
        var_dump($data);
        return ;
        return OrderGoods::insertGetId($data);
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
}