<?php

namespace app\backend\modules\charts\listeners;


use app\backend\modules\charts\modules\order\services\TimedTaskService;
use Illuminate\Foundation\Bus\DispatchesJobs;

class CommissionStatistic
{
    use DispatchesJobs;

    public function subscribe()
    {
        // \Event::listen('cron.collectJobs', function () {
            // \Cron::add('CommissionStatistic', '0 1 * * * *', function () {
                // (new TimedTaskService())->handle();
                // return;
            // });
        // });
    }
}