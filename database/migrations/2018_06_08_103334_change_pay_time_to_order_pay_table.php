<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangePayTimeToOrderPayTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        if (Schema::hasTable('yz_order_pay')) {
//            Schema::table('yz_order_pay', function (Blueprint $table) {
//                if (Schema::hasColumn('yz_order_pay', 'pay_time')) {
//                    $table->integer('pay_time')->nullable()->change();
//                    $table->integer('refund_time')->nullable()->change();
//                    //DB::select('update '.app('db')->getTablePrefix().'yz_order_pay set refund_time = null where refund_time = 0');
//                    //DB::select('update '.app('db')->getTablePrefix().'yz_order_pay set pay_time = null where pay_time = 0');
//                }
//            });
//        }
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
