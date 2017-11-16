<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
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

    /**
     * 后台付款
     */
    const PAY_BACKEND = 5;

    /**
     * 云收银-微信
     */
    const PAY_CLOUD_WEACHAT = 6;

    /**
     * 云收银-支付宝
     */
    const PAY_CLOUD_ALIPAY = 7;
    /**
     * APP-微信
     */
    const PAY_APP_WEACHAT = 9;
    /**
     * APP-支付宝
     */
    const PAY_APP_ALIPAY = 10;

    /**
     * 芸支付-微信
     */
    const PAY_YUN_WEACHAT = 12;

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
            case self::PAY_CLOUD_WEACHAT:
                $className = new \Yunshop\CloudPay\services\CloudPayService();
                break;
            case self::PAY_APP_WEACHAT:
                $className = new WechatPay();
                break;
            case self::PAY_APP_ALIPAY:
                $className = new AliPay();
                break;
            case self::PAY_YUN_WEACHAT:
                $className = new \Yunshop\YunPay\services\YunPayService();
                break;
            case self::PAY_CLOUD_ALIPAY:
                $className = new \Yunshop\CloudPay\services\CloudPayService();
                break;
            default:
                $className = null;
        }

        return $className;
    }
}