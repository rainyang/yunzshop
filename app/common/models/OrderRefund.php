<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/23
 * Time: 上午10:25
 */

namespace app\common\models;


class OrderRefund extends BaseModel
{
    protected $casts = [
        'images' => 'json',
        'refund_proof_imgs' => 'json',
    ];
    protected $attributes = [
        'images' => [],
        'reason' => '',
        'content' => '',
        'reply' => '',
        'refund_proof_imgs' => [],
        'remark' => ''
    ];
}