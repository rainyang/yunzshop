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
        $build = DB::table($this->tableName)
            ->select()
            ->where($this->foreignKey, $this->orderModel[$this->localKey]);
        // 分红记录IDs
        $ids = $build->pluck('id');
        // 分红总和
        $sum = $build->sum($this->amountColumn);
        if ($sum == 0) {
            return;
        }
        // 存入订单插件分红记录表
        OrderPluginBonus::addRow([
            'order_id'      => $this->orderModel->id,
            'table_name'    => $this->tableName,
            'ids'           => $ids,
            'code'          => $this->code,
            'amount'        => $sum
        ]);
        $this->updateAmount($sum);
    }

    private function updateAmount($sum)
    {
        // 验证表是否存在
        $exists_store = Schema::hasTable('yz_plugin_store_order');
        if (!$exists_store) {
            return;
        }
        $store_order = $this->getStoreOrder();
        if (!$store_order) {
            return;
        }
        $res_amount = 0;
        if ($store_order['amount'] - $sum > 0) {
            $res_amount = $store_order['amount'] - $sum;
        }
        if ($res_amount == 0) {
            return;
        }
        DB::table('yz_plugin_store_order')
            ->where('order_id', 2306)
            ->update(['amount' => $res_amount]);
    }

    private function getStoreOrder()
    {
        $store_order = DB::table('yz_plugin_store_order')->select()
            ->where('order_id', $this->orderModel->id)
            ->first();
        return $store_order;
    }
}