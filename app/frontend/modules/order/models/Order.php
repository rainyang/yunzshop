<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/4/6
 * Time: 下午4:35
 */

namespace app\frontend\modules\order\models;


class Order extends \app\common\models\Order
{
    protected $appends = ['status_name','pay_type_name','button_models'];

}