<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/9/19
 * Time: 下午3:37
 */

namespace app\Jobs;


use app\common\events\order\CreatedOrderPluginBonusEvent;
use app\common\models\order\OrderPluginBonus;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OrderBonusStatusJob implements  ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $orderId;

    public function __construct($orderId)
    {
        $this->orderId = $orderId;
    }

    public function handle()
    {
        OrderPluginBonus::updateStatus($this->orderId);
    }
}