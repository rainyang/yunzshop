<?php
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/3/24
 * Time: 下午8:14
 */

namespace app\common\services\statistics;


use app\Jobs\OrderCountJob;

class TimedTaskService
{
    /**
     * 佣金结算处理
     */
    public function handle()
    {
        \Log::info("--会员统计任务--");
        set_time_limit(0);

//        $uniAccount = UniAccount::get();

//        foreach ($uniAccount as $u) {
//            \YunShop::app()->uniacid = $u->uniacid;
//            Setting::$uniqueAccountId = $u->uniacid;
//        }
        dispatch(new OrderCountJob());

    }
}