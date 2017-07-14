<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/7/13
 * Time: 下午3:10
 */

namespace app\common\models;


use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoodsCouponQueue extends BaseModel
{
    use SoftDeletes;
    public $table = 'yz_goods_coupon_queue';

    
}