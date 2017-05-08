<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/5/8
 * Time: 下午5:06
 */

namespace app\backend\modules\order\models;


use app\backend\modules\goods\models\Goods;

class OrderGoods extends \app\common\models\OrderGoods
{
    public function goods()
    {
        return $this->hasOne(Goods::class, 'id', 'goods_id');
    }
}