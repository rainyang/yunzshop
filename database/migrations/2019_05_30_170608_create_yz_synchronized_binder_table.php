<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYzSynchronizedBinderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_synchronized_binder')) {
            Schema::create('yz_synchronized_binder', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->default(0);
                $table->integer('old_uid')->comment('fans表修改前的uid');
                $table->integer('new_uid')->comment('fans表修改后的uid');
                $table->integer('add_credit1')->comment('增加的积分');
                $table->integer('add_credit2')->comment('增加的余额');
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
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
        Schema::dropIfExists('yz_synchronized_binder');
    }
}
