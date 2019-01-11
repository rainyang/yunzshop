<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/24
 * Time: 11:37
 */

namespace app\backend\modules\charts\modules\phone\listeners;



use Illuminate\Foundation\Bus\DispatchesJobs;

class Team
{
    use DispatchesJobs;

    public function handle()
    {
        (new \TeamService())->OrderStatistics();
    }

    public function subscribe()
    {
        \Event::listen('cron.collectJobs', function () {
            \Cron::add('Team-Order', '0 3 1 * * *', function() {
                $this->handle();
                return;
            });
        });
    }
}