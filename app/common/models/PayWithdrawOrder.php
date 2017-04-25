<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2017/3/20
 * Time: 上午10:43
 */

namespace app\common\models;


class PayWithdrawOrder
{
    public $table = 'yz_withdraw_order';

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = ['uniacid', 'member_id', 'int_order_no', 'out_order_no', 'status', 'type', 'price'];

    public static function getOrderInfo($orderno)
    {
        return self::uniacid()
            ->where('out_order_no', $orderno)
            ->orderBy('id', 'desc')
            ->first();
    }
}