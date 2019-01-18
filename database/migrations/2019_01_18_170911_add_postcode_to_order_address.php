<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPostcodeToOrderAddress extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_order_address')) {
            Schema::table('yz_order_address', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_order_address', 'postcode')) {

                    $table->string('order_sn', 50);
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
        if (Schema::hasTable('yz_order_address')) {
            Schema::table('yz_order_address', function (Blueprint $table) {
                $table->dropColumn('postcode');
            });
        }
    }
}
