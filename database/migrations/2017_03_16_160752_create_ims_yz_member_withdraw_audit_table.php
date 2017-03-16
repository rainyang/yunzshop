<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzMemberWithdrawAuditTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_member_withdraw_audit', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('uniacid')->comment('统一公众号');
			$table->integer('member_withdraw_id')->index('idx_member_withdraw_audit_member_withdraw')->comment('提现');
			$table->integer('user_id')->comment('审核人');
			$table->integer('created_at')->comment('审核时间');
			$table->integer('status')->comment('审核状态');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ims_yz_member_withdraw_audit');
	}

}
