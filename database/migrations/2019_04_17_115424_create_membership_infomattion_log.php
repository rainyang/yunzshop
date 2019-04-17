<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMembershipInfomattionLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yz_membership_infomattion_log', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('uniacid');
            $table->integer('uid')->comment('用户修改信息');
            $table->string('old_data')->nullable()->comment('用户修改信息');
            $table->string('session_id')->nullable()->comment('session_id');
            $table->integer('created_at')->nullable();
            $table->integer('updated_at')->nullable();
            $table->integer('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('yz_membership_infomattion_log');
    }
}
