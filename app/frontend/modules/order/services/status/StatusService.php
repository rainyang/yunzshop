<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/2
 * Time: 下午4:51
 */

namespace app\frontend\modules\order\services\status;


interface StatusService
{
    const PAY = 1;
    const COMPLETE = 5;
    const EXPRESS =8;
    const CANCEL = 9;
    const COMMENT = 10;
    const ADD_COMMENT = 11;
    const DELETE = 12;
    const REFUND = 13;
    const VERIFY = 14;
    const AFTER_SALES = 15;
    const IN_REFUND = 16;
    const IN_AFTER_SALE = 17;
    function getStatusName();
    function getButtonModels();

}