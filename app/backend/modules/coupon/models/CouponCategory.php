<?php

namespace app\backend\modules\coupon\models;

use Illuminate\Database\Eloquent\Model;

class CouponCategory extends \app\common\models\CouponCategory
{
    static protected $needLog = true;

    public $table = 'yz_coupon_category';

    //
}
