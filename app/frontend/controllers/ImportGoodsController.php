<?php

namespace app\frontend\controllers;

use app\common\components\BaseController;
use app\common\models\ImportGoods;


class ImportGoodsController extends BaseController
{
    public function getGoods()
    {
        $goods_id = \YunShop::request()->goods_id;

        $goodsData = ImportGoods::getGoodsByIdAll($goods_id)->first();

        if($goodsData){
            return $this->successJson('ok', $goodsData->toArray());
        }

    }

}