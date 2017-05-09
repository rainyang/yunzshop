<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzMemberTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_member')) {
            Schema::create('yz_member', function (Blueprint $table) {
                $table->integer('member_id')->index('idx_member_id');
                $table->integer('uniacid')->index('idx_uniacid');
                $table->integer('agent_id')->nullable();
                $table->integer('group_id')->default(0);
                $table->integer('level_id')->default(0);
                $table->boolean('is_black')->default(0);
                $table->string('province', 3)->nullable();
                $table->string('city', 15)->nullable();
                $table->string('country', 10)->nullable();
                $table->string('referralsn')->nullable();
                $table->boolean('is_agent')->nullable();
                $table->string('alipayname')->nullable();
                $table->string('alipay')->nullable();
                $table->text('content', 65535)->nullable();
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
		Schema::drop('yz_member');
	}

}
