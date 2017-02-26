<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/21
 * Time: 下午2:58
 */

namespace app\frontend\modules\goods\model;


class Price
{
    private $_instance;
    private $goods_model;
    public function __construct(GoodsModel $goods_model)
    {
        $this->goods_model = $goods_model;
        $this->setDiscountWay();
    }

    public function getDiscountWay(){
        return Goods::getLastDBGoods()["discountway"];
    }
    public function setDiscountWay(){
        switch ($this->getDiscountWay()) {
            case 1:
                $this->_instance = new Discount($this->goods_model);
                break;
            case  2:
                $this->_instance = new MoneyOff($this->goods_model);
                break;
            default:
                $this->_instance = false;
                break;
        }
    }
    public function getDiscountPrice(){
        return $this->_instance->getDiscountPrice();
    }
}