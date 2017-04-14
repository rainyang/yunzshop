<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzMemberAppWechatTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_member_app_wechat', function(Blueprint $table)
		{
			$table->integer('app_wechat_id')->primary();
			$table->integer('uniacid')->comment('统一公众号ID');
			$table->integer('member_id')->comment('用户uid');
			$table->string('openid', 50)->comment('唯一用户标识');
			$table->string('nickname', 20)->comment('昵称');
			$table->string('avatar')->comment('头像');
			$table->boolean('gender')->default(0)->comment('性别 0-男；1-女');
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
		Schema::drop('ims_yz_member_app_wechat');
	}

}
