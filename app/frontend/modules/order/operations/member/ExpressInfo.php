<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/8/1
 * Time: 下午6:43
 */
namespace app\frontend\models\order\member;

use app\frontend\models\order\OrderOperation;

class ExpressInfo extends OrderOperation
{
    public function getName()
    {
        return '物流信息';
    }

    public function getValue()
    {
        return 8;
    }
}