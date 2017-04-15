<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/4/14
 * Time: 下午10:57
 */

namespace app\frontend\modules\goods\models;



class GoodsOption extends \app\common\models\GoodsOption
{

    public function goods()
    {
        $this->belongsTo(Goods::class,'goods_id','id');
    }
}