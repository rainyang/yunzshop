<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/5
 * Time: 15:47
 */

namespace app\backend\modules\charts\listeners;


use Illuminate\Foundation\Bus\DispatchesJobs;

class Statistics
{
    use DispatchesJobs;

    public function subscribe()
    {
        \Event::listen('cron.collectJobs', function () {
            \Cron::add('Statistics', '0 1 * * * *', function () {
                (new \app\common\services\statistics\TimedTaskService())->handle();
                return;
            });
        });
    }
}