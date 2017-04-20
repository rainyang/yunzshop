<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2017/3/20
 * Time: 上午10:41
 */

namespace app\common\models;

use app\backend\models\BackendModel;

class PayLog extends BackendModel
{
    public $table = 'yz_pay_log';

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = ['uniacid', 'member_id', 'type', 'third_type', 'price', 'operation', 'ip'];
}