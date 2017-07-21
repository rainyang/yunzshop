<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/29
 * Time: 上午10:27
 */

namespace app\common\services\finance;


use app\common\facades\Setting;
use Illuminate\Support\Facades\Log;

class MessageService
{

    public static function incomeWithdraw($withdrawData, $member, $uniacid = '')
    {
        if(!\YunShop::notice()->getNotSend('withdraw.incone_withdraw_title')){
            return;
        }
        if ($uniacid) {
            Setting::$uniqueAccountId = $uniacid;
        }
        $withdrawNotice = Setting::get('withdraw.notice');
        if ($withdrawNotice['template_id'] && ($member['follow'] == 1)) {
            $message = $withdrawNotice['incone_withdraw'];
            $message = str_replace('[昵称]', $member['nickname'], $message);
            $message = str_replace('[时间]', date('Y-m-d H:i:s', time()), $message);
            $message = str_replace('[收入类型]', $withdrawData['type_name'], $message);
            $message = str_replace('[金额]', $withdrawData['amounts'], $message);
            $message = str_replace('[手续费]', $withdrawData['poundage'], $message);
            $payWay = WithdrawService::getPayWayService($withdrawData['pay_way']);
            $message = str_replace('[提现方式]', $payWay, $message);
            $msg = [
                "first" => '您好',
                "keyword1" => $withdrawNotice['incone_withdraw_title'] ? $withdrawNotice['incone_withdraw_title'] : '提现申请通知',
                "keyword2" => $message,
                "remark" => "",
            ];
            \app\common\services\MessageService::notice($withdrawNotice['template_id'], $msg, $member['openid'], $uniacid);
        }
        return;
    }

    public static function withdrawCheck($withdrawData, $member, $uniacid = '')
    {
        if(!\YunShop::notice()->getNotSend('withdraw.incone_withdraw_check_title')){
            return;
        }
        if ($uniacid) {
            Setting::$uniqueAccountId = $uniacid;
        }
        $withdrawNotice = Setting::get('withdraw.notice');
        if ($withdrawNotice['template_id'] && ($member['follow'] == 1)) {
            $message = $withdrawNotice['incone_withdraw_check'];
            $message = str_replace('[昵称]', $member['nickname'], $message);
            $message = str_replace('[时间]', date('Y-m-d H:i:s', time()), $message);
            $message = str_replace('[收入类型]', $withdrawData['type_name'], $message);
            $message = str_replace('[金额]', $withdrawData['amounts'], $message);
            $message = str_replace('[状态]', $withdrawData['status'], $message);
            $message = str_replace('[审核通过金额]', $withdrawData['actual_amounts'], $message);
            $message = str_replace('[手续费]', $withdrawData['actual_poundage'], $message);
            $payWay = WithdrawService::getPayWayService($withdrawData['pay_way']);
            $message = str_replace('[提现方式]', $payWay, $message);
            $msg = [
                "first" => '您好',
                "keyword1" => $withdrawNotice['incone_withdraw_check_title'] ? $withdrawNotice['incone_withdraw_check_title'] : '提现审核通知',
                "keyword2" => $message,
                "remark" => "",
            ];
            \app\common\services\MessageService::notice($withdrawNotice['template_id'], $msg, $member['openid'], $uniacid);
        }
        return;
    }
    public static function withdrawPay($withdrawData, $member, $uniacid = '')
    {
        if(!\YunShop::notice()->getNotSend('withdraw.incone_withdraw_pay_title')){
            return;
        }
        if ($uniacid) {
            Setting::$uniqueAccountId = $uniacid;
        }
        $withdrawNotice = Setting::get('withdraw.notice');
        if ($withdrawNotice['template_id'] && ($member['follow'] == 1)) {
            $message = $withdrawNotice['incone_withdraw_pay'];
            $message = str_replace('[昵称]', $member['nickname'], $message);
            $message = str_replace('[时间]', date('Y-m-d H:i:s', time()), $message);
            $message = str_replace('[收入类型]', $withdrawData['type_name'], $message);
            $message = str_replace('[金额]', $withdrawData['actual_amounts'], $message);
            $message = str_replace('[状态]', $withdrawData['pay_status'], $message);
            $payWay = WithdrawService::getPayWayService($withdrawData['pay_way']);
            $message = str_replace('[提现方式]', $payWay, $message);
            $msg = [
                "first" => '您好',
                "keyword1" => $withdrawNotice['incone_withdraw_pay_title'] ? $withdrawNotice['incone_withdraw_pay_title'] : '提现打款通知',
                "keyword2" => $message,
                "remark" => "",
            ];
            \app\common\services\MessageService::notice($withdrawNotice['template_id'], $msg, $member['openid'], $uniacid);
        }
        return;
    }
    public static function withdrawArrival($withdrawData, $member, $uniacid = '')
    {
        if(!\YunShop::notice()->getNotSend('withdraw.incone_withdraw_arrival_title')){
            return;
        }
        if ($uniacid) {
            Setting::$uniqueAccountId = $uniacid;
        }
        $withdrawNotice = Setting::get('withdraw.notice');
        if ($withdrawNotice['template_id'] && ($member['follow'] == 1)) {
            $message = $withdrawNotice['incone_withdraw_arrival'];
            $message = str_replace('[昵称]', $member['nickname'], $message);
            $message = str_replace('[时间]', date('Y-m-d H:i:s', time()), $message);
            $message = str_replace('[收入类型]', $withdrawData['type_name'], $message);
            $message = str_replace('[金额]', $withdrawData['actual_amounts'], $message);
            $message = str_replace('[状态]', $withdrawData['pay_status'], $message);
            $payWay = WithdrawService::getPayWayService($withdrawData['pay_way']);
            $message = str_replace('[提现方式]', $payWay, $message);
            $msg = [
                "first" => '您好',
                "keyword1" => $withdrawNotice['incone_withdraw_arrival_title'] ? $withdrawNotice['incone_withdraw_arrival_title'] : '提现到账通知',
                "keyword2" => $message,
                "remark" => "",
            ];
            \app\common\services\MessageService::notice($withdrawNotice['template_id'], $msg, $member['openid'], $uniacid);
        }
        return;
    }
}