<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/23
 * Time: 上午11:16
 */
namespace app\frontend\modules\order\model\factory;

use app\frontend\modules\order\model\PreGeneratedOrderModel;
use app\frontend\modules\order\model\PreGeneratedOrderGoodsModel;


class PreGeneratedOrderModelFactory extends OrderModelFactory
{

    public function createOrderModel(array $pre_generated_order_goods_models=null){

        return (new PreGeneratedOrderModel($pre_generated_order_goods_models));
    }

}