<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/6
 * Time: 上午10:12
 */

namespace app\frontend\modules\coin\deduction\models;


class OrderGoodsDeduction
{
    private $orderGoods;
    private $deduction;
    public function deduction()
    {
        return $this->belongsTo(Deduction::class, 'code', 'code');
    }
}