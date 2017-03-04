<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYzPermissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yz_permission', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('type')->comment('1:user 2:role 3:account');
            $table->integer('item_id')->comment('目标ID:user_id role_id uniacid');
            $table->string('permission')->comment('权限值');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('yz_permission');
    }
}
