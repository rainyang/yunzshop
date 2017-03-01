<?php
namespace app\frontend\modules\goods\service;
use app\frontend\modules\goods\model\factory\GoodsModelFactory;

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/21
 * Time: 下午4:01
 */
class GoodsService
{
    public static function getGoodsModels($goods_id_arr){
        return GoodsModelFactory::createModels($goods_id_arr);
    }
    public static function getGoodsModel($goods_id){
        return GoodsModelFactory::createModel($goods_id);
    }
}