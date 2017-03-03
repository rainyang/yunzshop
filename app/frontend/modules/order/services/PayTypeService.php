<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/2/27
 * Time: 上午10:01
 */

namespace app\frontend\modules\order\services;

use app\common\models\Order;


class PayTypeService
{
    //获取所有的支付方式
    public static function getAllPayWay($order, $openid, $uniacid)
    {
        //$set      = m('common')->getSysset();
        //load()->model('payment');
        //$setting = uni_setting($_W['uniacid'], array('payment'));
        $set = array();
        $setting = array();
        $pay_ways = array();

        //余额支付
        $credit        = array(
            'success' => false
        );
        if (isset($set['pay']) && $set['pay']['credit'] == 1) {
            if ($order['deductcredit2'] <= 0) {
                $credit = array(
                    'success' => true,
                    //'current' => m('member')->getCredit($openid, 'credit2')
                    'current' => '100000'
                );
            }
        }
        $pay_ways[] = $credit;

        //app阿里支付
        $app_alipay = array(
            'success' => false
        );
        if (isset($set['pay']) && $set['pay']['app_alipay'] == 1) {
            $app_alipay['success'] = true;
        }
        $pay_ways[] = $app_alipay;

        //app微信支付
        $app_wechat = array(
            'success' => false
        );
        if (isset($set['pay']) && $set['pay']['app_weixin'] == 1) {
            $app_wechat['success'] = true;
        }
        $pay_ways[] = $app_wechat;

        //微信支付
        $wechat  = array(
            'success' => false,
            'qrcode' => false
        );
        $jie = $set['pay']['weixin_jie'];
        if (is_weixin()) {
            if (isset($set['pay']) && ($set['pay']['weixin'] == 1) && ($jie != 1)) {
                if (is_array($setting['payment']['wechat']) && $setting['payment']['wechat']['switch']) {
                    $wechat['success'] = true;
                    $wechat['weixin'] = true;
                    $wechat['weixin_jie'] = false;
                }
            }
        }
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            if ((isset($set['pay']) && ($set['pay']['weixin_jie'] == 1) && !$wechat['success']) || ($jie == 1)) {
                $wechat['success'] = true;
                $wechat['weixin_jie'] = true;
                $wechat['weixin'] = false;
            }
        }
        $wechat['jie'] = $jie;
        //扫码
        if (!isMobile() && isset($set['pay']) && $set['pay']['weixin'] == 1) {
            if (isset($set['pay']) && $set['pay']['weixin'] == 1) {
                if (is_array($setting['payment']['wechat']) && $setting['payment']['wechat']['switch']) {
                    $wechat['qrcode'] = true;
                }
            }
        }
        $pay_ways[] = $app_alipay;

        //阿里支付
        $alipay = array(
            'success' => false
        );
        if (isset($set['pay']) && $set['pay']['alipay'] == 1) {
            if (is_array($setting['payment']['alipay']) && $setting['payment']['alipay']['switch']) {
                $alipay['success'] = true;
            }
        }
        $pay_ways[] = $app_alipay;

        //银联支付
        $unionpay = array(
            'success' => false
        );
        if (isset($set['pay']) && $set['pay']['unionpay'] == 1) {
            if (is_array($setting['payment']['unionpay']) && $setting['payment']['unionpay']['switch']) {
                $unionpay['success'] = true;
            }
        }
        $pay_ways[] = $app_alipay;

        //易宝支付
        $yeepay = array(
            'success' => false
        );
        if (isset($set['pay']) && $set['pay']['yeepay'] == 1) {
            $yeepay['success'] = true;
        }
        $pay_ways[] = $app_alipay;

        //paypal支付
        $paypal = array(
            'success' => false
        );
        if (isset($set['pay']) && $set['pay']['paypalstatus'] == 1){
            $paypal['success'] = true;
        }
        $pay_ways[] = $app_alipay;

        return $pay_ways;
    }

    //验证当前支付方式
    public static function verifyPay($pay_type, $pay_way)
    {
        if (!in_array($pay_type, $pay_way)) {
            return show_json(0, '未找到支付方式');
        }
    }

    //验证用户余额是否足够
    public static function verifyMemberCredit($openid, $order)
    {
        //$member = m('member')->getInfo($openid);
        $member = array();
        if($member['credit2'] < $order['deductcredit2'] && $order['deductcredit2'] > 0){
            return show_json(0, '余额不足，请充值后在试！');
        }
    }
}