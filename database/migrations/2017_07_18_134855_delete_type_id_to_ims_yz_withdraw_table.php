<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteTypeIdToImsYzWithdrawTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('yz_withdraw', function (Blueprint $table) {
            if (Schema::hasColumn('yz_withdraw', 'type_id')) {
                $table->dropColumn('type_id');
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
        Schema::table('yz_withdraw', function (Blueprint $table) {
            $table->dropColumn('type_id');
        });
    }
}