<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDataToStatusFlowTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_status_flow')) {
            return;
        }
        if (!Schema::hasTable('yz_status')) {
            return;
        }
        if (!Schema::hasTable('yz_status_flow_status')) {
            return;
        }
        $a = \app\common\models\StatusFlow::make([
            'name' => '订单汇款支付',
            'code' => 'orderRemittancePay',
            'plugin_id' => 0,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
