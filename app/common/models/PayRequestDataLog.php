<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2017/3/20
 * Time: 上午10:45
 */

namespace app\common\models;


class PayRequestDataLog
{
    public $table = 'yz_pay_request_data';

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = ['uniacid', 'order_id', 'params', 'type', 'third_type', 'price', 'ip'];
}