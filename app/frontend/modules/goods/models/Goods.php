<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/31
 * Time: 下午5:55
 */

namespace app\frontend\modules\goods\models;

use app\frontend\modules\order\services\OrderService;

class Goods extends \app\common\models\Goods
{
    public function getOptionIdAttribute()
    {
        OrderService::getSelectedOptionId();
        //从参数中获取option_id,参数从哪里获取
    }

    public function hasOneOptions()
    {
        return $this->hasOne('app\common\models\GoodsOption');
    }


}