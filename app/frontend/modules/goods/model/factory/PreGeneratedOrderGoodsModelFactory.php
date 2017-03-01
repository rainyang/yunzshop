<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/28
 * Time: 下午3:32
 */

namespace app\frontend\modules\goods\model\factory;


use app\frontend\modules\goods\model\PreGeneratedOrderGoods;
use app\frontend\modules\goods\service\GoodsService;

class PreGeneratedOrderGoodsModelFactory
{
    public function createOrderGoodsModels(array $param){
        $result = [];
        $goods_models = $this->getGoodsModels($param);

        foreach ($goods_models as $goods_model){

            $total = $this->getTotal($goods_model,$param);
            $order_goods_model =new PreGeneratedOrderGoods();
            $order_goods_model->setGoodsModel($goods_model);
            $order_goods_model->setTotal($total);

            $result[] = $order_goods_model;

        }

        return $result;
    }
    public function createOrderGoodsModel( $goods_model,$total=1){
        $order_goods_model =new PreGeneratedOrderGoods();
        $order_goods_model->setGoodsModel($goods_model);
        $order_goods_model->setTotal($total);
        return $order_goods_model;
    }
    //todo 待实现
    private function getTotal($goods_model,$param){
        return $param[$goods_model->id][1];
    }
    //todo 待实现
    private function getGoodsModel($item){
        return GoodsService::getGoodsModel();
    }

}