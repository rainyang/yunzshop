<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/3/31
 * Time: 10:36 PM
 */

namespace app\common\services\finance;


use app\common\models\UniAccount;
use app\Jobs\PointQueueJob;
use Illuminate\Foundation\Bus\DispatchesJobs;

class PointQueueService
{
    use DispatchesJobs;

    public function handle()
    {
        $uniAccount = UniAccount::getEnable() ?: [];
        foreach ($uniAccount as $u) {
            \YunShop::app()->uniacid = $u->uniacid;
            \Setting::$uniqueAccountId = $u->uniacid;
            $this->run();
        }
    }

    private function run()
    {
        $setLog = \Setting::get('point_queue.return_log');
        $returnAt = date('y').'-'.date('m').'-'.date('d');

        if ($setLog['return_at'] == $returnAt) {
            \Log::info('UNIACID:' . \YunShop::app()->uniacid . ' - ' . $returnAt . '已返现,当前不可返现[积分奖励]');
            return;
        }

        $setLog['return_at'] = $returnAt;
        \Setting::set('point_queue.return_log', $setLog);

        $this->dispatch(new PointQueueJob(\YunShop::app()->uniacid));
        //(new PointQueueJob(\YunShop::app()->uniacid))->handle();
    }
}