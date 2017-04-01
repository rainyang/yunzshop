<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/28
 * Time: 下午3:32
 */

namespace app\frontend\modules\goods\services\models\factory;

use app\frontend\modules\goods\services\models\PreGeneratedOrderGoodsModel;

class PreGeneratedOrderGoodsModelFactory
{
    //todo 数组传入参数调用困难
    public function createOrderGoodsModels($memberCarts)
    {
        $result = [];

        foreach ($memberCarts as $memberCart) {
            $orderGoodsModel = new PreGeneratedOrderGoodsModel($memberCart->goods, $memberCart->total);
            $result[] = $orderGoodsModel;
        }

        return $result;
    }
}