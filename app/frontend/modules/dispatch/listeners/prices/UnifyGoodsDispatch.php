<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/11
 * Time: 上午10:00
 */

namespace app\frontend\modules\dispatch\listeners\prices;

use app\common\events\dispatch\OrderGoodsDispatchWasCalculated;

class UnifyGoodsDispatch
{
    public function handle(OrderGoodsDispatchWasCalculated $even)
    {

        if (!$this->needDispatch()) {
            return;
        }

        $even->addData($this->getDispatchDetail());

        return;
    }

    public function needDispatch(){
        return true;
    }
    //todo 从商品中获取运费信息
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