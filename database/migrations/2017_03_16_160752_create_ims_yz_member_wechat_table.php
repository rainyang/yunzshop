<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzMemberWechatTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_member_wechat', function(Blueprint $table)
		{
			$table->integer('wechat_id', true);
			$table->integer('uniacid')->comment('统一公众号ID');
			$table->integer('member_id')->index('idx_member_id')->comment('用户uid');
			$table->string('openid', 50)->comment('唯一用户标识');
			$table->string('nickname', 20)->comment('昵称');
			$table->boolean('gender')->default(0)->comment('性别 0-男；1-女');
			$table->string('avatar')->comment('头像');
			$table->string('province', 4)->comment('省');
			$table->string('city', 25)->comment('市');
			$table->string('country', 10)->comment('国家');
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
		Schema::drop('ims_yz_member_wechat');
	}

}
