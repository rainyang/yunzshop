<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/4/20
 * Time: 下午8:12
 */

namespace app\common\models\refund\type;


use app\common\models\refund\RefundApply;

class RefundMoney extends RefundApply
{
    protected $refund_type_name = '退款(仅退款不退货)';



}