<?php
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/4/5
 * Time: 下午7:36
 */

namespace app\common\models\finance;


use app\common\models\Order;

class IncomeOrder extends Order
{
    public function commissionorders()
    {
        return $this->morphMany('Yunshop\Commission\models\CommissionOrder', 'ordertable');
    }


}