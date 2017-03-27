<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/28
 * Time: 下午1:44
 */

namespace app\frontend\modules\goods\services\models;

class PreGeneratedOrderGoodsModelGroup
{
    private $_OrderGoodsGroup;
    public function __construct(array $OrderGoodsGroup)
    {
        $this->_OrderGoodsGroup = $OrderGoodsGroup;
    }

    public function getPrice(){
        $result = 0;
        foreach ($this->_OrderGoodsGroup as $OrderGoods){
            $result += $OrderGoods->getPrice;
        }
        return $result;
    }
}