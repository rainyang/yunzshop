<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/24
 * Time: 11:37
 */

namespace app\backend\modules\charts\modules\phone\listeners;



use app\backend\modules\charts\modules\team\services\TeamService;
use Illuminate\Foundation\Bus\DispatchesJobs;

class Team
{
    use DispatchesJobs;

    public function handle()
    {
        (new TeamService())->OrderStatistics();
    }

    public function subscribe()
    {
        \Event::listen('cron.collectJobs', function () {
            \Cron::add('Month-Order', '22 14 12 * * *', function() {
                $this->handle();
                return;
            });
        });
    }
}