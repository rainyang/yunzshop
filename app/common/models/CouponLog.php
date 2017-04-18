<?php

namespace app\common\models;

use app\common\models\BaseModel;

class CouponLog extends BaseModel
{
    public $table = 'yz_coupon_log';
    public $guarded = [];
    public $timestamps = false;
}