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
    public function createOrderGoodsModels(array $param)
    {
        $result = [];
        $goodsModels = $this->getGoodsModels($param);

        foreach ($goodsModels as $goodsModel) {
            $total = $this->getTotal($goodsModel->id, $param);

            $orderGoodsModel = $this->createOrderGoodsModel($goodsModel, $total);
            $result[] = $orderGoodsModel;
        }

        return $result;
    }

    private function createOrderGoodsModel($goodsModel, $total = 1)
    {
        dd($goodsModel);
        exit;
        if(isset($goodsModel->hasManyOption)){

        }
        $result = new PreGeneratedOrderGoodsModel($goodsModel, $total);
        return $result;
    }

    //todo 待完善(缓存)
    private function getTotal($goods_id, $param)
    {
        $goodsTotalArr = array_column($param, 'total', 'goods_id');
        return $goodsTotalArr[$goods_id];
    }

    private function getGoodsModels($param)
    {

        return GoodsService::getGoodsModels($param);
    }

}