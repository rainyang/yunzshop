<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzMemberRechargeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_member_recharge', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('uniacid')->comment('统一公众号');
			$table->integer('member_id')->comment('会员');
			$table->boolean('type')->comment('充值类型：1微信2支付宝3易宝');
			$table->integer('money')->comment('金额');
			$table->integer('created_at')->comment('创建时间');
			$table->integer('updated_at')->comment('最后更新时间');
			$table->boolean('status')->comment('状态：1完成2未完成0失败');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ims_yz_member_recharge');
	}

}
