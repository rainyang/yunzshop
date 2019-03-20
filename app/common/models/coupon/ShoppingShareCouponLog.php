<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/20
 * Time: 10:50
 */

namespace app\common\models\coupon;


use app\common\models\BaseModel;

class ShoppingShareCouponLog extends BaseModel
{
    public $table = 'yz_shopping_share_coupon_log';

    protected $guarded = ['id'];

    protected $attributes = [
        'status' => 0,
    ];

}