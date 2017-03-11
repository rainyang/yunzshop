<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/23
 * Time: 上午11:16
 */
namespace app\frontend\modules\order\services\models\factory;

use app\frontend\modules\order\services\models\PreGeneratedOrderModel;
use app\frontend\modules\order\services\models\PreGeneratedOrderGoodsModel;


class PreGeneratedOrderModelFactory
{

    public function createOrderModel(array $pre_generated_order_goods_models=null){

        return (new PreGeneratedOrderModel($pre_generated_order_goods_models));
    }

}