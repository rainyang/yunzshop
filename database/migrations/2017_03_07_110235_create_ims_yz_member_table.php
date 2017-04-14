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
		Schema::create('yz_member', function(Blueprint $table)
		{
			$table->integer('member_id')->index('idx_member_id');
			$table->integer('uniacid')->index('idx_uniacid')->comment('统一公众号');
			$table->integer('agent_id')->nullable()->comment('分销商ID');
			$table->integer('group_id')->default(0)->comment('用户组ID');
			$table->integer('level_id')->default(0)->comment('会员等级ID');
			$table->boolean('is_black')->default(0)->comment('0-普通会员;1-黑名单会员');
			$table->string('province', 3)->nullable()->comment('省');
			$table->string('city', 15)->nullable()->comment('市');
			$table->string('country', 10)->nullable()->comment('国家');
			$table->string('referralsn')->nullable()->comment('推荐码');
			$table->boolean('is_agent')->nullable();
			$table->string('alipayname')->nullable()->comment('支付宝姓名');
			$table->string('alipay')->nullable()->comment('支付宝账号');
			$table->text('content', 65535)->nullable()->comment('备注');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ims_yz_member');
	}

}
