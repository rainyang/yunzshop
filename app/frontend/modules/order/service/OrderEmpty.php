<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/3/1
 * Time: 下午10:42
 */

namespace app\frontend\modules\order\services;

use app\frontend\modules\order\services\model\behavior;

class OrderEmpty
{
    public function isEmpty($order)
    {
        if (empty($order)) {
            message("抱歉，订单不存在!", referer(), "error");
        }
    }
}