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

class OrderBonusContentJob implements  ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $tableName;
    protected $code;
    protected $foreignKey;
    protected $localKey;
    protected $contentColumn;
    protected $orderModel;
    protected $condition;

    public function __construct($orderModel)
    {
        $this->orderModel = $orderModel;
    }

    public function handle()
    {
       $this->address();
       $this->buyName();
       $this->referrerName();
       $this->shopName();
    }

    public function address()
    {
        $build = DB::table('yz_order_address')
            ->select()
            ->where('order_id', $this->orderModel->id);
        $ids = $build->pluck('id');
        $content = $build->first()['address'];
        if (empty($content)) {
            return;
        }
        $model = OrderPluginBonus::addRow([
            'order_id'      => $this->orderModel->id,
            'table_name'    => 'yz_order_address',
            'ids'           => $ids,
            'code'          => 'address',
            'amount'        => 0,
            'content'       => $content,
            'status'        => 0,
        ]);
    }

    public function buyName()
    {
        $build = DB::table('mc_member')
            ->select()
            ->where('uid', $this->orderModel->uid);
        $ids = $build->pluck('id');
        $content = $build->first()['nickname'];
        if (empty($content)) {
            return;
        }
        $model = OrderPluginBonus::addRow([
            'order_id'      => $this->orderModel->id,
            'table_name'    => 'mc_member',
            'ids'           => $ids,
            'code'          => 'buy_name',
            'amount'        => 0,
            'content'       => $content,
            'status'        => 0,
        ]);
    }

    public function referrerName()
    {
        $referrerTable = DB::table('yz_member')
            ->select()
            ->where('member_id', $this->orderModel->uid);
        $parent_id = $referrerTable->first()['parent_id'];

        $build = DB::table('mc_member')
            ->select()
            ->where('uid', $parent_id);
        $ids = $build->pluck('id');
        $content = $build->first()['nickname'];
        if (empty($content)) {
            return;
        }
        $model = OrderPluginBonus::addRow([
            'order_id'      => $this->orderModel->id,
            'table_name'    => 'mc_member',
            'ids'           => $ids,
            'code'          => 'referrer_name',
            'amount'        => 0,
            'content'       => $content,
            'status'        => 0,
        ]);
    }

    public function shopName()
    {
        if ($this->orderModel->is_plugin) {
            $supplierTable = DB::table('yz_supplier_order')
                ->select()
                ->where('order_id', $this->orderModel->id);
            $supplier_id = $supplierTable->first()['supplier_id'];
            $build = DB::table('yz_supplier')
                ->select()
                ->where('id', $supplier_id);
            $ids = $build->pluck('id');
            $content = $build->first()['username'];
            if (empty($content)) {
                return;
            }
            $model = OrderPluginBonus::addRow([
                'order_id'      => $this->orderModel->id,
                'table_name'    => 'yz_supplier',
                'ids'           => $ids,
                'code'          => 'shop_name',
                'amount'        => 0,
                'content'       => $content,
                'status'        => 0,
            ]);
        } elseif ($this->orderModel->plugin_id == 31) {
            $cashierTable = DB::table('yz_plugin_cashier_order')
                ->select()
                ->where('order_id', $this->orderModel->id);
            $cashier_id = $cashierTable->first()['cashier_id'];
            $build = DB::table('yz_store')
                ->select()
                ->where('cashier', $cashier_id);
            $ids = $build->pluck('id');
            $content = $build->first()['store_name'];
            if (empty($content)) {
                return;
            }
            $model = OrderPluginBonus::addRow([
                'order_id'      => $this->orderModel->id,
                'table_name'    => 'yz_store',
                'ids'           => $ids,
                'code'          => 'shop_name',
                'amount'        => 0,
                'content'       => $content,
                'status'        => 0,
            ]);
        } elseif ($this->orderModel->plugin_id == 32) {
            $storeTable = DB::table('yz_plugin_store_order')
                ->select()
                ->where('order_id', $this->orderModel->id);
            $store_id = $storeTable->first()['store_id'];
            $build = DB::table('yz_store')
                ->select()
                ->where('id', $store_id);
            $ids = $build->pluck('id');
            $content = $build->first()['store_name'];
            if (empty($content)) {
                return;
            }
            $model = OrderPluginBonus::addRow([
                'order_id'      => $this->orderModel->id,
                'table_name'    => 'yz_store',
                'ids'           => $ids,
                'code'          => 'shop_name',
                'amount'        => 0,
                'content'       => $content,
                'status'        => 0,
            ]);
        } else {
            $model = OrderPluginBonus::addRow([
                'order_id'      => $this->orderModel->id,
                'table_name'    => 'yz_shop',
                'ids'           => 0,
                'code'          => 'shop_name',
                'amount'        => 0,
                'content'       => '平台自营',
                'status'        => 0,
            ]);
        }
    }
}