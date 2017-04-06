<?php
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/4/5
 * Time: 下午7:36
 */

namespace app\backend\modules\finance\models;


use app\common\models\Order;

class IncomeOrder extends Order
{
    public function commissionorders()
    {
        return $this->morphMany('Yunshop\Commission\models\CommissionOrder', 'ordertable');
    }


}