<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzMemberMiniAppTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_member_mini_app', function(Blueprint $table)
		{
			$table->integer('mini_app_id', true);
			$table->integer('uniacid')->comment('统一公众号ID');
			$table->integer('member_id')->comment('用户uid');
			$table->string('openid', 50)->comment('用户唯一标识');
			$table->string('nickname', 20)->comment('昵称');
			$table->string('avatar')->comment('头像');
			$table->boolean('gender')->comment('性别0-男；1-女');
			$table->integer('created_at')->unsigned()->default(0)->comment('创建时间');
			$table->integer('updated_at')->unsigned()->default(0);
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
		Schema::drop('ims_yz_member_mini_app');
	}

}
