<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/24
 * Time: 下午4:50
 */

namespace app\frontend\modules\goods\model\factory;
use app\common\models\Goods;
use app\frontend\modules\goods\model\GoodsGroupModel;

class GoodsGroupModelFactory
{
    public static function getGoodsGroupModel(){
        $goods_group_model = new GoodsGroupModel();
        $goods = self::_getSourceByORM();
        foreach ($goods as $goods_item){
            $goods_group_model->addGoods((new GoodsModelFactory())->getGoodsModel($goods_item));
        }
        return $goods_group_model;
    }
    private static function _getSourceByORM(){
        $goods = Goods::first()->offset(0)->limit(2);
        return $goods;
    }
}