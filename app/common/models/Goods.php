<?php
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/2/28
 * Time: 上午11:31
 */

namespace app\common\models;

use app\common\models\BaseModel;

class Goods extends BaseModel
{
    public $table = 'yz_goods';

    //public $fillable = ['display_order'];

    public $guarded = [];

    public static function getList()
    {
        return parent::find();
    }

    public static function getGoodsById($id)
    {
        return parent::find($id);
    }
    
    public function hasManyParams()
    {
        return $this->hasMany('app\common\models\GoodsParam');
    }
}
//class Goods extends BaseModel
//{
//    public $table = 'yz_goods';
//
//    public static function getGoods($goods_id, $uniacid)
//    {
//
//        return self::where('id', $goods_id)
//            ->where('uniacid', $uniacid)
//            ->first()
//            ->toArray();
//    }
//}