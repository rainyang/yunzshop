<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/4/6
 * Time: 下午4:35
 */

namespace app\frontend\modules\order\models;


use app\frontend\models\Member;

class Order extends \app\common\models\Order
{
    protected $appends = ['status_name','pay_type_name','button_models'];
    public function belongsToMember()
    {
        return $this->belongsTo(Member::class, 'uid', 'uid');
    }
}