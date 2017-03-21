<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/21
 * Time: 上午11:02
 */

namespace app\common\events\discount;


use app\common\events\order\PreGenerateOrderEvent;
use app\frontend\modules\order\services\models\PreGeneratedOrderModel;

class OnDiscountInfoDisplayEvent extends PreGenerateOrderEvent
{

}