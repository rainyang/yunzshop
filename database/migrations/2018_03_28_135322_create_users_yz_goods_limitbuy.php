<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableYzGoodsLimitbuy extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_goods_limitbuy')) {

            Schema::create('yz_goods_limitbuy', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->unsigned()->index('idx_uinacid');
                $table->integer('goods_id')->unsigned()->index('idx_uid');
                $table->integer('status');
                $table->integer('start_time', 11);
                $table->integer('end_time', 11);
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
        if (Schema::hasTable('ims_yz_goods_limitbuy')) {

            Schema::drop('ims_yz_goods_limitbuy');
        }
    }
}
