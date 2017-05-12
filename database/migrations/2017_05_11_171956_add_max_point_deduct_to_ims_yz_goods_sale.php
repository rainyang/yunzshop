<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMaxPointDeductToImsYzGoodsSale extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('yz_goods_sale', function (Blueprint $table) {
            if (Schema::hasColumn('yz_goods_sale', 'max_point_deduct')) {
                $table->string('max_point_deduct', 255)->nullable();
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
        Schema::table('yz_goods_sale', function (Blueprint $table) {
            if (Schema::hasColumn('yz_goods_sale', 'max_point_deduct')) {
                $table->dropColumn('max_point_deduct');
            }
        });
    }
}
