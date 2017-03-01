<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/23
 * Time: 上午11:16
 */
namespace app\frontend\modules\goods\model\factory;

use app\common\models\Goods;
use app\frontend\modules\goods\model\GoodsModel;

class GoodsModelFactory
{
    public static function createModels($para){
        $result = [];
        foreach (self::getFromOrm($para) as $goods_model){
            $result[] = new GoodsModel($goods_model);
        }
        return $result;
    }
    public static function createModel($goods_id){
        $result = new GoodsModel(Goods::find($goods_id));
        return $result;

    }
    public static function getFromOrm($para){
        return Goods::all()->take(2);
    }
}