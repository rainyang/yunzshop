<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/28
 * Time: 下午3:32
 */

namespace app\frontend\modules\goods\services\models\factory;


use app\frontend\modules\goods\services\models\PreGeneratedOrderGoodsModel;
use app\frontend\modules\goods\services\GoodsService;

class PreGeneratedOrderGoodsModelFactory
{
    //todo 数组传入参数调用困难
    public function createOrderGoodsModels(array $param){
        $result = [];
        $goods_models = $this->getGoodsModels($param);

        foreach ($goods_models as $goods_model){

            $total = $this->getTotal($goods_model->id,$param);

            $order_goods_model =new PreGeneratedOrderGoodsModel($goods_model,$total);

            $result[] = $order_goods_model;

        }

        return $result;
    }
    public function createOrderGoodsModel($goods_model,$total=1){
        $order_goods_model =new PreGeneratedOrderGoodsModel();
        $order_goods_model->setGoodsModel($goods_model);
        $order_goods_model->setTotal($total);
        return $order_goods_model;
    }
    //todo 待完善(缓存)
    private function getTotal($goods_id,$param){
        $goods_total_arr = array_column($param,'total','goods_id');

        return $goods_total_arr[$goods_id];
    }
    private function getGoodsModels($param){
        $goods_id_arr = array_column($param,'goods_id');
        return GoodsService::getGoodsModels($goods_id_arr);
    }
    //todo 待完善
    private function getGoodsModel($item){
        $goods_id = $item['goods_id'];
        return GoodsService::getGoodsModel($goods_id);
    }

}