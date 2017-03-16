<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/11
 * Time: 上午10:00
 */

namespace app\frontend\modules\goods\listeners;

use app\common\events\order\OrderGoodsDispatchWasCalculated;

class UnifyGoodsDispatch
{
    public function handle(OrderGoodsDispatchWasCalculated $even)
    {

        if (!$this->needDispatch()) {
            return;
        }

        $even->goods_dispatch_obj->addDispatchDetail($this->getDispatchDetail());

        return;
    }

    public function needDispatch(){
        return true;
    }
    public function getDispatchDetail(){
        $detail = [
            'name'=>'统一运费',
            'id'=>1,
            'value'=>'19',
            'price'=>'19',
            'plugin'=>'0',
        ];
        return $detail;
    }
}