<?php

namespace app\frontend\modules\order\fee;

use app\frontend\models\order\PreOrderFee;

class GoodsFee extends BaseOrderFee
{
    protected $code = 'goods_fee';
    protected $name = '商品手续费';

    protected function _getAmount()
    {
        $amount = 100000;
        //遍历订单下所有商品,拿到合计手续费
        return $amount;
    }

}