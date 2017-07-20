<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOrderSnToBalanceTransfer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('yz_balance_transfer', function(Blueprint $table)
        {
            if (!Schema::hasColumn('yz_balance_transfer', 'order_sn')) {

                $table->string('order_sn',255)->nullable();
            }

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('yz_balance_transfer', function (Blueprint $table) {
            $table->dropColumn('order_sn');
        });
    }
}
