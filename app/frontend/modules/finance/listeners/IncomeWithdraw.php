<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/2
 * Time: 上午10:59
 */
namespace app\frontend\modules\finance\listeners;

use app\common\events\finance\AfterIncomeWithdrawArrivalEvent;
use app\common\events\finance\AfterIncomeWithdrawCheckEvent;
use app\common\events\finance\AfterIncomeWithdrawEvent;
use app\common\events\finance\AfterIncomeWithdrawPayEvent;
use app\common\models\Member;
use app\common\services\finance\MessageService;
use app\common\services\finance\WithdrawService;
use Illuminate\Support\Facades\Log;

class IncomeWithdraw
{
    /**
     * 提现申请
     * @param AfterIncomeWithdrawEvent $event
     */
    public function withdraw(AfterIncomeWithdrawEvent $event)
    {
        $data = $event->getData();
        foreach ($data as $item) {
            $member = Member::getMemberByUid($item['member_id'])->with('hasOneFans')->first();
            $noticeData = [
                'type_name' => $item['type_name'],
                'amounts' => $item['amounts'],
                'poundage' => $item['poundage'],
                'pay_way' => $item['pay_way'],
            ];
            MessageService::incomeWithdraw($noticeData,$member);
        }

    }

    /**
     * 提现审核
     * @param AfterIncomeWithdrawCheckEvent $event
     */
    public function withdrawCheck(AfterIncomeWithdrawCheckEvent $event)
    {
        $data = $event->getData();
        $member = Member::getMemberByUid($data->member_id)->with('hasOneFans')->first();
        $withdrawStatusName = WithdrawService::getWithdrawStatusName($data->status);
        $noticeData = [
            'type_name' => $data->type_name,
            'amounts' => $data->amounts,
            'status' => $withdrawStatusName,
            'actual_amounts' => $data->actual_amounts,
            'actual_poundage' => $data->actual_poundage,
            'pay_way' => $data->pay_way,
        ];
        MessageService::withdrawCheck($noticeData,$member);
    }

    /**
     * 提现打款支付
     * @param AfterIncomeWithdrawPayEvent $event
     */
    public function withdrawPay(AfterIncomeWithdrawPayEvent $event)
    {
        $data = $event->getData();
        $member = Member::getMemberByUid($data->member_id)->with('hasOneFans')->first();
        $payStatusName = WithdrawService::getPayStatusName($data->pay_status);
        $noticeData = [
            'type_name' => $data->type_name,
            'pay_status' => $payStatusName,
            'actual_amounts' => $data->actual_amounts,
            'pay_way' => $data->pay_way,
        ];
        MessageService::withdrawPay($noticeData,$member);
    }

    /**
     * 提心打款到账
     * @param AfterIncomeWithdrawArrivalEvent $event
     */
    public function withdrawArrival(AfterIncomeWithdrawArrivalEvent $event)
    {
        $data = $event->getData();
        $member = Member::getMemberByUid($data->member_id)->with('hasOneFans')->first();
        $payStatusName = WithdrawService::getPayStatusName($data->pay_status);
        $noticeData = [
            'type_name' => $data->type_name,
            'pay_status' => $payStatusName,
            'actual_amounts' => $data->actual_amounts,
            'pay_way' => $data->pay_way,
        ];
        MessageService::withdrawArrival($noticeData,$member);
    }

    public function subscribe($events)
    {
        $events->listen(
            AfterIncomeWithdrawEvent::class,
            self::class . '@withdraw'
        );
        $events->listen(
            AfterIncomeWithdrawCheckEvent::class,
            self::class . '@withdrawCheck'
        );
        $events->listen(
            AfterIncomeWithdrawPayEvent::class,
            self::class . '@withdrawPay'
        );
        $events->listen(
            AfterIncomeWithdrawArrivalEvent::class,
            self::class . '@withdrawArrival'
        );
    }
}