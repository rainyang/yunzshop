<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNoRefundToYzGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_goods')) {
            Schema::table('yz_goods', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_member_cart', 'no_refund')) {
                    $table->tinyInteger('no_refund')->default(0);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('yz_goods')) {
            if (Schema::hasColumn('yz_goods', 'no_refund')) {
                Schema::table('yz_goods', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->dropColumn('no_refund');
                });
            }
        }
    }
}
