<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/9/19
 * Time: 下午3:37
 */

namespace app\Jobs;


use app\common\models\order\OrderPluginBonus;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OrderBonusJob implements  ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $tableName;
    protected $code;
    protected $foreignKey;
    protected $localKey;
    protected $amountColumn;
    protected $orderModel;

    public function __construct($tableName, $code, $foreignKey, $localKey, $amountColumn, $orderModel)
    {
        $this->tableName = $tableName;
        $this->code = $code;
        $this->foreignKey = $foreignKey;
        $this->localKey = $localKey;
        $this->amountColumn = $amountColumn;
        $this->orderModel = $orderModel;
    }

    public function handle()
    {
        // yz_order_bonus   id order_id table_name code amount
        // $tableName = yz_commission_order
        // $code = commission
        // $foreignKey = yz_commission_order.ordertable_id
        // $localKey = yz_order.id
        // $amountColumn = yz_commission_order.commission
        // 查询 $tableName 下 $foreignKey=$orderModel->$localKey $amountColumn 的 sum

        // 验证表是否存在
        $exists_table = Schema::hasTable($this->tableName);
        if (!$exists_table) {
            return;
        }
        // 分红总和
        $sum = DB::table($this->tableName)
            ->select()
            ->where($this->foreignKey, $this->orderModel[$this->localKey])
            ->sum($this->amountColumn);
        if ($sum == 0) {
            return;
        }
        // 存入订单插件分红记录表
        OrderPluginBonus::addRow([
            'order_id'      => $this->orderModel->id,
            'table_name'    => $this->tableName,
            'code'          => $this->code,
            'amount'        => $sum
        ]);
    }
}