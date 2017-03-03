<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/23
 * Time: 上午11:16
 */
namespace app\frontend\modules\goods\services\models\factory;

use app\common\models\Goods;
use app\frontend\modules\goods\services\models\GoodsModel;

class GoodsModelFactory
{
    public static function createModels($goods_id_arr){
        return self::getFromOrm($goods_id_arr);
        /*$result = [];
        foreach (self::getFromOrm($goods_id_arr) as $goods_model){
            $result[] = new GoodsModel($goods_model);
        }
        return $result;*/
    }
    public static function createModel($goods_id){
        return Goods::find($goods_id);
        /*$result = new GoodsModel(Goods::find($goods_id));
        return $result;*/

    }
    public static function getFromOrm($goods_id_arr){
        return Goods::select()->whereIn('id',$goods_id_arr)->get();
    }
}