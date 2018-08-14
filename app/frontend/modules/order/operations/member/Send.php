<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/8/1
 * Time: 下午6:43
 */

namespace app\frontend\modules\order\operations\member;

use app\frontend\modules\order\operations\OrderOperation;

class Send extends OrderOperation
{
    public function getName()
    {
        return '确认发货';
    }

    public function getValue()
    {
        return ;
    }

    public function enable()
    {

        return true;
    }
}