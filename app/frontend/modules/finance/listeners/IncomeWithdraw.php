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

class IncomeWithdraw
{
    /**
     * 提现申请
     * @param AfterIncomeWithdrawEvent $event
     */
    public function withdraw(AfterIncomeWithdrawEvent $event)
    {
        $data = $event->getData();
    }

    /**
     * 提现审核
     * @param AfterIncomeWithdrawCheckEvent $event
     */
    public function withdrawCheck(AfterIncomeWithdrawCheckEvent $event)
    {
        $data = $event->getData();
    }

    /**
     * 提现打款支付
     * @param AfterIncomeWithdrawPayEvent $event
     */
    public function withdrawPay(AfterIncomeWithdrawPayEvent $event)
    {
        $data = $event->getData();
    }

    /**
     * 提心打款到账
     * @param AfterIncomeWithdrawArrivalEvent $event
     */
    public function withdrawArrival(AfterIncomeWithdrawArrivalEvent $event)
    {
        $data = $event->getData();
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