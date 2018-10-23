<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/7/25
 * Time: 下午7:10
 */

namespace app\common\models\order;

use app\common\models\BaseModel;

class OrderCoupon extends BaseModel
{
    public $table = 'yz_order_coupon';
    protected $fillable = [];
    protected $guarded = ['id'];
}