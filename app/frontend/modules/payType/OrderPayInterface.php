<?php
namespace app\frontend\modules\orderPay\payType;

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/7
 * Time: 下午4:54
 */
interface OrderPayInterface
{
    function getPayParams($option);
    function applyPay($option);
}