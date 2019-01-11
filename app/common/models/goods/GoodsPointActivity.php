<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/11
 * Time: 16:58
 */

namespace app\common\models\goods;


use app\common\models\BaseModel;

class GoodsPointActivity extends BaseModel
{
    public $table = 'yz_goods_point_activity';
    protected $guarded = [''];

    static function getDataByGoodsId($goods_id)
    {
        return self::uniacid()
            ->where('goods_id', $goods_id)
            ->first();
    }
}