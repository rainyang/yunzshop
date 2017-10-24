<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/17
 * Time: 下午2:40
 */

namespace app\frontend\modules\coin;


use app\common\models\VirtualCoin;

class InvalidVirtualCoin extends VirtualCoin
{
    protected function _getCode()
    {
        return 'invalid';
    }

    protected function _getExchangeRate()
    {
        return 0;
    }

    protected function _getName()
    {
        return '无';
    }
}