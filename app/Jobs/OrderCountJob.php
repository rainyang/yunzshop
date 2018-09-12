<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/8/2
 * Time: 11:08
 */

namespace app\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use app\common\services\statistics\OrderCountService;

class OrderCountJob implements ShouldQueue
{

    use InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        (new OrderCountService())->statistics();
    }
}