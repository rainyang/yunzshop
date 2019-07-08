<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/29
 * Time: 上午10:27
 */

namespace app\common\services\finance;


use app\common\facades\Setting;
use app\common\models\notice\MessageTemp;
use Illuminate\Support\Facades\Log;

class MessageService
{

    public static function incomeWithdraw($withdrawData, $member, $uniacid = '')
    {
        Log::info("收入提现提交通知开始");
        if (!\YunShop::notice()->getNotSend('withdraw.income_withdraw_title')) {
            return;
        }
        if ($uniacid) {
            Setting::$uniqueAccountId = $uniacid;
        }
        $withdrawNotice = Setting::get('withdraw.notice');
        $temp_id = $withdrawNotice['income_withdraw'];
        Log::info("收入提现提交通知",print_r($temp_id,true));
        if (!$temp_id) {
            return;
        }
        static::messageNotice($temp_id, $member, $withdrawData, $uniacid);
//        if ($withdrawNotice['template_id']) {
//            $message = $withdrawNotice['income_withdraw'];
//            $message = str_replace('[昵称]', $member['nickname'], $message);
//            $message = str_replace('[时间]', date('Y-m-d H:i:s', time()), $message);
//            $message = str_replace('[收入类型]', $withdrawData['type_name'], $message);
//            $message = str_replace('[金额]', $withdrawData['amounts'], $message);
//            $message = str_replace('[手续费]', $withdrawData['poundage'], $message);
//            $payWay = WithdrawService::getPayWayService($withdrawData['pay_way']);
//            $message = str_replace('[提现方式]', $payWay, $message);
//            $msg = [
//                "first" => '您好',
//                "keyword1" => $withdrawNotice['income_withdraw_title'] ? $withdrawNotice['income_withdraw_title'] : '提现申请通知',
//                "keyword2" => $message,
//                "remark" => "",
//            ];
//            \app\common\services\MessageService::notice($withdrawNotice['template_id'], $msg, $member['uid'], $uniacid);
//        }
//        return;
    }

    public static function withdrawCheck($withdrawData, $member, $uniacid = '')
    {
        if (!\YunShop::notice()->getNotSend('withdraw.income_withdraw_check_title')) {
            return;
        }
        if ($uniacid) {
            Setting::$uniqueAccountId = $uniacid;
        }
        $withdrawNotice = Setting::get('withdraw.notice');
        $temp_id = $withdrawNotice['income_withdraw_check'];
        if (!$temp_id) {
            return;
        }
        $withdrawData['poundage'] = $withdrawData['actual_poundage'];
        static::messageNotice($temp_id, $member, $withdrawData, $uniacid);
//        if ($withdrawNotice['template_id']) {
//            $message = $withdrawNotice['income_withdraw_check'];
//            $message = str_replace('[昵称]', $member['nickname'], $message);
//            $message = str_replace('[时间]', date('Y-m-d H:i:s', time()), $message);
//            $message = str_replace('[收入类型]', $withdrawData['type_name'], $message);
//            $message = str_replace('[金额]', $withdrawData['amounts'], $message);
//            $message = str_replace('[状态]', $withdrawData['status'], $message);
//            $message = str_replace('[审核通过金额]', $withdrawData['actual_amounts'], $message);
//            $message = str_replace('[手续费]', $withdrawData['actual_poundage'], $message);
//            $payWay = WithdrawService::getPayWayService($withdrawData['pay_way']);
//            $message = str_replace('[提现方式]', $payWay, $message);
//            $msg = [
//                "first" => '您好',
//                "keyword1" => $withdrawNotice['income_withdraw_check_title'] ? $withdrawNotice['income_withdraw_check_title'] : '提现审核通知',
//                "keyword2" => $message,
//                "remark" => "",
//            ];
//            \app\common\services\MessageService::notice($withdrawNotice['template_id'], $msg, $member['uid'], $uniacid);
//        }
//        return;
    }

    public static function withdrawPay($withdrawData, $member, $uniacid = '')
    {
        if (!\YunShop::notice()->getNotSend('withdraw.income_withdraw_pay_title')) {
            return;
        }
        if ($uniacid) {
            Setting::$uniqueAccountId = $uniacid;
        }
        $withdrawNotice = Setting::get('withdraw.notice');
        $temp_id = $withdrawNotice['income_withdraw_pay'];
        if (!$temp_id) {
            return;
        }
        $withdrawData['amounts'] = $withdrawData['actual_amounts'];
        $withdrawData['status'] = $withdrawData['pay_status'];
        static::messageNotice($temp_id, $member, $withdrawData, $uniacid);
//        if ($withdrawNotice['template_id']) {
//            $message = $withdrawNotice['income_withdraw_pay'];
//            $message = str_replace('[昵称]', $member['nickname'], $message);
//            $message = str_replace('[时间]', date('Y-m-d H:i:s', time()), $message);
//            $message = str_replace('[收入类型]', $withdrawData['type_name'], $message);
//            $message = str_replace('[金额]', $withdrawData['actual_amounts'], $message);
//            $message = str_replace('[状态]', $withdrawData['pay_status'], $message);
//            $payWay = WithdrawService::getPayWayService($withdrawData['pay_way']);
//            $message = str_replace('[提现方式]', $payWay, $message);
//            $msg = [
//                "first" => '您好',
//                "keyword1" => $withdrawNotice['income_withdraw_pay_title'] ? $withdrawNotice['income_withdraw_pay_title'] : '提现打款通知',
//                "keyword2" => $message,
//                "remark" => "",
//            ];
//            \app\common\services\MessageService::notice($withdrawNotice['template_id'], $msg, $member['uid'], $uniacid);
//        }
//        return;
    }

    public static function withdrawArrival($withdrawData, $member, $uniacid = '')
    {
        if (!\YunShop::notice()->getNotSend('withdraw.income_withdraw_arrival_title')) {
            return;
        }
        if ($uniacid) {
            Setting::$uniqueAccountId = $uniacid;
        }
        $withdrawNotice = Setting::get('withdraw.notice');
        $temp_id = $withdrawNotice['income_withdraw_arrival'];
        if (!$temp_id) {
            return;
        }
        $withdrawData['amounts'] = $withdrawData['actual_amounts'];
        $withdrawData['status'] = $withdrawData['pay_status'];
        static::messageNotice($temp_id, $member, $withdrawData, $uniacid);
//        if ($withdrawNotice['template_id']) {
//            $message = $withdrawNotice['income_withdraw_arrival'];
//            $message = str_replace('[昵称]', $member['nickname'], $message);
//            $message = str_replace('[时间]', date('Y-m-d H:i:s', time()), $message);
//            $message = str_replace('[收入类型]', $withdrawData['type_name'], $message);
//            $message = str_replace('[金额]', $withdrawData['actual_amounts'], $message);
//            $message = str_replace('[状态]', $withdrawData['pay_status'], $message);
//            $payWay = WithdrawService::getPayWayService($withdrawData['pay_way']);
//            $message = str_replace('[提现方式]', $payWay, $message);
//            $msg = [
//                "first" => '您好',
//                "keyword1" => $withdrawNotice['income_withdraw_arrival_title'] ? $withdrawNotice['income_withdraw_arrival_title'] : '提现到账通知',
//                "keyword2" => $message,
//                "remark" => "",
//            ];
//            \app\common\services\MessageService::notice($withdrawNotice['template_id'], $msg, $member['uid'], $uniacid);
//        }
//        return;
    }

    //收入提现失败通知
    public static function withdrawFailure($withdrawData, $member, $uniacid = '')
    {
        if (!\YunShop::notice()->getNotSend('withdraw.income_withdraw_arrival_title')) {
            \Log::debug('income_withdraw_arrival_title----not--send';
            return;
        }
        if ($uniacid) {
            Setting::$uniqueAccountId = $uniacid;
        }
        $withdrawNotice = Setting::get('withdraw.notice');
        $temp_id = $withdrawNotice['income_withdraw_fail'];
        if (!$temp_id) {
            \Log::debug('收入提现失败通知发送失败, 无该模板:'.$temp_id);
            return;
        }
        // $withdrawData['amounts'] = $withdrawData['actual_amounts'];
        // $withdrawData['withdraw_sn'] = $withdrawData['withdraw_sn'];
        // $withdrawData['poundage'] = $withdrawData['actual_poundage'];

        static::messageNotice($temp_id, $member, $withdrawData, $uniacid);
    }

    public static function messageNotice($temp_id, $member, $data = [], $uniacid = '')
    {
        $payWay = WithdrawService::getPayWayService($data['pay_way']);
        $params = [
            ['name' => '昵称', 'value' => $member['nickname']],
            ['name' => '时间', 'value' => date('Y-m-d H:i:s', time())],
            ['name' => '收入类型', 'value' => $data['type_name']],
            ['name' => '金额', 'value' => $data['amounts']],
            ['name' => '手续费', 'value' => $data['poundage']],
            ['name' => '提现方式', 'value' => $payWay],
            ['name' => '状态', 'value' => $data['status']],
            ['name' => '审核通过金额', 'value' => $data['actual_amounts']],
            ['name' => '提现单号', 'value' => $data['withdraw_sn']],
        ];

        $msg = MessageTemp::getSendMsg($temp_id, $params);
        if (!$msg) {
            return;
        }
        \app\common\services\MessageService::notice(MessageTemp::$template_id, $msg, $member->uid ? : $member, $uniacid);
    }
}