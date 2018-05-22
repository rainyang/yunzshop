<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGoodsVideoToGoods extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
           if (Schema::hasTable('yz_goods')) {
            if (!Schema::hasColumn('yz_goods', 'goods_video')) {
                Schema::table('yz_goods', function (Blueprint $table) {
                    $table->string('goods_video', 255)->nullable()->default('');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
