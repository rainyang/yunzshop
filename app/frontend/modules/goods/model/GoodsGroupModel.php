<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/24
 * Time: 下午4:42
 */

namespace app\frontend\modules\goods\model;

use app\frontend\modules\goods\model\Price\GoodsGroupDispatchPrice;

class GoodsGroupModel
{
    private $cache;
    protected $_goods_models;

    public function __construct()
    {

    }

    public function addGoods(GoodsModel $goods_model)
    {
        $this->_goods_models[] = $goods_model;
    }
    public function getTotal(){
        $result = 0;
        foreach ($this->_goods_models as $goods_model) {
            $result += $goods_model->getData()['total'];
        }
        return $result;
    }
    public function getDispatchPrice()
    {
        return (new GoodsGroupDispatchPrice($this->_goods_models))->getPrice();
    }

    public function getDiscountPrice()
    {
        $result = 0;
        foreach ($this->_goods_models as $goods_model) {
            $result += $goods_model->getDiscountPrice() * $goods_model->getData()['total'];
        }
        return $result;
    }

    public function getMarketPrice()
    {
        $result = 0;
        foreach ($this->_goods_models as $goods_model) {
            $result += $goods_model->getData()['marketprice'] * $goods_model->getData()['total'];
        }
        return $result;
    }

    public function getFinalPrice()
    {
        return $this->getMarketPrice() - $this->getDiscountPrice();
    }
}