<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AChangePayTimeToOrderPayTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_order_pay')) {
            Schema::table('yz_order_pay', function (Blueprint $table) {
                DB::select("ALTER TABLE `".app('db')->getTablePrefix().$table->getTable()."` CHANGE `refund_time` `refund_time` INT(11)  NULL  DEFAULT NULL");
                DB::select("ALTER TABLE `".app('db')->getTablePrefix().$table->getTable()."` CHANGE `pay_time` `pay_time` INT(11)  NULL  DEFAULT NULL");

            });

            DB::select('update ' . app('db')->getTablePrefix() . 'yz_order_pay set refund_time = null where refund_time = 0');
            DB::select('update ' . app('db')->getTablePrefix() . 'yz_order_pay set pay_time = null where pay_time = 0');
        }
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
