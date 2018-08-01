<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/17
 * Time: 下午1:34
 */

namespace app\common\services;


use app\common\exceptions\AppException;

class PayFactory
{
    /**
     * 微信
     */
    const PAY_WEACHAT = 1;

    /**
     * 支付宝
     */
    const PAY_ALIPAY = 2;

    /**
     * 余额
     */
    const PAY_CREDIT = 3;

//    /**
//     * 后台付款
//     */
//    const PAY_BACKEND = 5;

    /**
     * 云收银-微信
     */
    const PAY_CLOUD_WEACHAT = 6;

    /**
     * 云收银-支付宝
     */
    const PAY_CLOUD_ALIPAY = 7;
    /**
     * 现金支付
     */
    const PAY_CASH = 8;
    /**
     * APP-微信
     */
    const PAY_APP_WEACHAT = 9;
    /**
     * APP-支付宝
     */
    const PAY_APP_ALIPAY = 10;
    /**
     * 门店支付
     */
    const PAY_STORE = 11;
    /**
     * 微信-YZ
     */
    const PAY_YUN_WEACHAT = 12;


    /**
     * 支付宝-YZ
     */
    const PAY_YUN_ALIPAY = 15;


    /**
     * 转账
     */
    const PAY_REMITTANCE = 16;

    /**
     * 货到付款
     */
    const PAY_COD = 17;

    /**
     * 环迅快捷支付
     */
    const PAY_Huanxun_Quick = 18;


    /**
     * EUP-支付
     */
    const PAY_EUP = 19;

    /**
     *威富通公众号支付
     */
    const WFT_PAY = 20;

    /**
     *威富通支付宝支付
     */
    const WFT_ALIPAY = 21;


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
                if (!app('plugins')->isEnabled('cloud-pay')) {
                    throw new AppException('插件未开启');
                }

                $className = new \Yunshop\CloudPay\services\CloudPayService();
                break;
            case self::PAY_APP_WEACHAT:
                $className = new WechatPay();
                break;
            case self::PAY_APP_ALIPAY:
                $className = new AliPay();
                break;
            case self::PAY_STORE:
                $className = new StorePay();
                break;
            case self::PAY_YUN_WEACHAT:
                if (!app('plugins')->isEnabled('yun-pay')) {
                    throw new AppException('插件未开启');
                }

                $className = new \Yunshop\YunPay\services\YunPayService();
                break;
            case self::PAY_CLOUD_ALIPAY:
                if (!app('plugins')->isEnabled('cloud-pay')) {
                    throw new AppException('插件未开启');
                }

                $className = new \Yunshop\CloudPay\services\CloudPayService();
                break;
            case self::PAY_YUN_ALIPAY:
                if (!app('plugins')->isEnabled('yun-pay')) {
                    throw new AppException('插件未开启');
                }

                $className = new \Yunshop\YunPay\services\YunPayService();
                break;
            case self::PAY_Huanxun_Quick:
                if (!app('plugins')->isEnabled('huanxun')) {
                    throw new AppException('插件未开启');
                }

                $className = new \Yunshop\Huanxun\services\HuanxunPayService();
                break;
            case self::PAY_EUP:
                if (!app('plugins')->isEnabled('eup-pay')) {
                    throw new AppException('插件未开启');
                }

                $className = new \Yunshop\EupPay\services\EupWithdrawService();
                break;
            case self::PAY_REMITTANCE:
                $className = new RemittancePay();
                break;
            case self::PAY_COD:
                $className = new CODPay();
                break;
            case self::WFT_PAY:
                if (!app('plugins')->isEnabled('wft-pay')) {
                    throw new AppException('插件未开启');
                }

                $className = new \Yunshop\WftPay\services\WftPayService();
                break;
            case self::WFT_ALIPAY:
                if (!app('plugins')->isEnabled('wft-alipay')) {
                    throw new AppException('插件未开启');
                }

                $className = new \Yunshop\WftAlipay\services\WftAlipayService();
                break;
            default:
                $className = null;
        }

        return $className;
    }

    public static function pay($type, $data)
    {
        $pay = self::create($type);

        if ($type == self::PAY_CLOUD_ALIPAY) {
            $data['extra']['pay'] = 'cloud_alipay';
        }

        $result = $pay->doPay($data);

        switch ($type) {
            case self::PAY_WEACHAT:
            case self::PAY_CREDIT:
                if (is_bool($result)) {
                    $result = (array)$result;
                }

                $trade = \Setting::get('shop.trade');
                $redirect = '';

                if (!is_null($trade) && isset($trade['redirect_url']) && !empty($trade['redirect_url'])) {
                    $redirect = $trade['redirect_url'];
                }

                $result['redirect'] = $redirect;
        }

        return $result;
    }
}