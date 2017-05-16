<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWithdrawToImsYzCommissionOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('yz_commission_order', function(Blueprint $table)
        {
            if (!Schema::hasColumn('yz_commission_order', 'withdraw')) {

                $table->tinyInteger('withdraw')->default(0);
            }

        });    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('yz_commission_order', function (Blueprint $table) {
            $table->dropColumn('withdraw');
        });
    }
}
