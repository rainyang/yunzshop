<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzMemberWithdrawTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_member_withdraw', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('uniacid');
			$table->integer('member_id')->comment('会员');
			$table->integer('type')->comment('类型：1微信2支付宝3银行卡4余额');
			$table->integer('money')->comment('金额');
			$table->integer('created_at')->comment('创建时间');
			$table->integer('updated_at')->comment('最后更新时间');
			$table->boolean('status')->comment('状态 1完成2未完成0失败');
			$table->boolean('audit_status')->comment('审核状态  1审核中 2通过 3拒绝');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ims_yz_member_withdraw');
	}

}
