<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/27
 * Time: 上午9:50
 */

namespace app\frontend\modules\goods\model\price\preferential;


use app\frontend\modules\goods\model\GoodsModel;

class NoDiscount
{
    private $_goods_model;

    public function __construct(GoodsModel$_goods_model)
    {
        $this->_goods_model = $_goods_model;
    }
    public function getDiscountPrice()
    {
        return $this->_goods_model->getInitialData()['marketprice'];
    }
}