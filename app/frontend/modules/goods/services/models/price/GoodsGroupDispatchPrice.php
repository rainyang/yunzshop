<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/24
 * Time: 下午2:03
 */
namespace app\frontend\modules\goods\services\models\Price;

class GoodsGroupDispatchPrice
{
    private $_goods_models;
    public function __construct(array $goods_models)
    {
        $this->_goods_models = $goods_models;
    }
    private function _getTemplatePrice(){
        $result = 0;
        foreach ($this->_goods_models as $goods_model){
            $result += $goods_model->getTemplatePrice();
        }
        return $result;
    }
    public function getPrice(){
        return $this->_getTemplatePrice() + $this->_getUnifyPrice();
    }
    private function _getUnifyPrice(){
        $result = 0;
        foreach ($this->_goods_models as $goods_model){
            $result += $goods_model->getUnifyPrice();
        }
        return $result;
    }
}