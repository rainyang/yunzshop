<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/11/7
 * Time: 14:29
 */

namespace app\Jobs;

use app\backend\modules\charts\models\OrderIncomeCount;
use app\common\events\order\CreatedOrderPluginBonusEvent;
use app\common\models\order\OrderPluginBonus;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OrderCountAddJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $code;
    public $sum;
    public $undividend;
    public $goods_id;
    public $count;

    public function __construct($code, $sum, $undividend, $goods_id, $count = 0)
    {
        $this->code = $code;
        $this->sum = $sum;
        $this->undividend = $undividend;
        $this->goods_id = $goods_id;
        $this->count = $count + 1;
    }

    public function handle()
    {
        $field = str_replace('-','_',$this->code);
        $order_income = OrderIncomeCount::where('order_id', $this->goods_id)->first();
        if ($order_income) {
            if ($order_income->$field > 0) {
                return true;
            }
            $order_income->$field = $this->sum;
            $order_income->undividend += $this->undividend;
            $order_income->save();

        } else {
            //执行5次后放弃队列
            if ($this->count > 5) {
                return true;
            }
            //一分钟后继续查询
            $job = new OrderCountAddJob($this->code, $this->sum, $this->undividend, $this->goods_id, $this->count);
            $job = $job->delay(60);
            dispatch($job);
            return true;
        }
    }
}