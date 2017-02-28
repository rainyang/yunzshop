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
    
    public static function getGoods($goods_id, $uniacid)
    {
        
        return self::where('id', $goods_id)
            ->where('uniacid', $uniacid)
            ->first()
            ->toArray();
    }
}