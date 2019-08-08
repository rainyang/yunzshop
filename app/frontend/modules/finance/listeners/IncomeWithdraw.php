<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/2
 * Time: 上午10:59
 */
namespace app\frontend\modules\finance\listeners;

use app\common\events\withdraw\WithdrawAppliedEvent;
use app\common\events\withdraw\WithdrawAuditedEvent;
use app\common\events\withdraw\WithdrawPayedEvent;
use app\common\events\withdraw\WithdrawPayingEvent;
use app\common\models\Member;
use app\common\services\finance\MessageService;

class IncomeWithdraw
{
    /**
     * 提现申请
     * @param WithdrawAppliedEvent $event
     */
    public function withdraw($event)
    {
        $withdrawModel = $event->getWithdrawModel();

        $member = Member::getMemberByUid($withdrawModel->member_id)->with('hasOneFans')->first();
        $noticeData = [
            'type_name' => $withdrawModel->type_name,
            'amounts' => $withdrawModel->amounts,
            'poundage' => $withdrawModel->poundage,
            'pay_way' => $withdrawModel->pay_way,
        ];
        MessageService::incomeWithdraw($noticeData,$member);
    }

    /**
     * 提现审核
     * @param WithdrawAuditedEvent $event
     */
    public function withdrawCheck($event)
    {
        $withdrawModel = $event->getWithdrawModel();

        $member = Member::getMemberByUid($withdrawModel->member_id)->with('hasOneFans')->first();
        $noticeData = [
            'type_name'     => $withdrawModel->type_name,
            'amounts'       => $withdrawModel->amounts,
//            'status'        => "已审核",
            'actual_amounts'    => $withdrawModel->actual_amounts,
            'actual_poundage'   => $withdrawModel->actual_poundage,
            'pay_way'       => $withdrawModel->pay_way,
        ];
        if ($withdrawModel->status == 1) {
            $noticeData['status'] = "审核通过";

        } elseif ($withdrawModel->status == 2) {
            $noticeData['status'] = "驳回";

        } elseif ($withdrawModel->status == -1) {
            $noticeData['status'] = "无效";

        }
        MessageService::withdrawCheck($noticeData,$member);
    }

    /**
     * 提现打款支付
     * @param WithdrawPayingEvent $event
     */
    public function withdrawPay($event)
    {
        $withdrawModel = $event->getData();

        $member = Member::getMemberByUid($withdrawModel->member_id)->with('hasOneFans')->first();
        $noticeData = [
            'type_name' => $withdrawModel->type_name,
            'pay_status' => "已打款",
            'actual_amounts' => $withdrawModel->actual_amounts,
            'pay_way' => $withdrawModel->pay_way,
        ];
        MessageService::withdrawPay($noticeData,$member);
    }

    /**
     * 提心打款到账
     * @param WithdrawPayedEvent $event
     */
    public function withdrawArrival($event)
    {
        $withdrawModel = $event->getWithdrawModel();
        \Log::debug('---------withdrawModel,,model+++++++++-----------------');

        $member = Member::getMemberByUid($withdrawModel->member_id)->with('hasOneFans')->first();
        \Log::debug('---------member_uids+++++-----------------');

        $noticeData = [
            'type_name' => $withdrawModel->type_name,
            'pay_status' => "已到账",
            'actual_amounts' => $withdrawModel->actual_amounts,
            'pay_way' => $withdrawModel->pay_way,
        ];
        \Log::debug('---------$noticeData+++++-----------------');

        MessageService::withdrawArrival($noticeData,$member);
    }

    public function subscribe($events)
    {
        $events->listen(
            WithdrawAppliedEvent::class,
            self::class . '@withdraw'
        );
        $events->listen(
            WithdrawAuditedEvent::class,
            self::class . '@withdrawCheck'
        );
        $events->listen(
            WithdrawPayingEvent::class,
            self::class . '@withdrawPay'
        );
        $events->listen(
            WithdrawPayedEvent::class,
            self::class . '@withdrawArrival'
        );
    }
}