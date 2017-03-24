<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2017/3/17
 * Time: 下午1:34
 */

namespace app\common\services;


class PayFactory
{
    /**
     * 微信
     */
    const PAY_WEACHAT = 1;

    /**
     * 支付宝
     */
    const PAY_ALIPAY  = 2;

    /**
     * 余额
     */
    const PAY_CREDIT  = 3;

    /**
     * 货到付款
     */
    const PAY_CASH = 4;

   public static function create($type = null)
    {
        $className = null;

        switch ($type) {
            case self::PAY_WEACHAT:
                $className = new WechatPay();
                break;
            case self::PAY_ALIPAY:
                $className = new AliPay();
                break;
            case self::PAY_CREDIT:
                $className = new CreditPay();
                break;
            case self::PAY_CASH:
                $className = new CashPay();
                break;
            default:
                $className = null;
        }

        return $className;
    }
}