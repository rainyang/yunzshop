<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzMemberMoneyRecordTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_member_money_record', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('uniacid')->comment('统一公众号');
			$table->integer('member_id')->comment('会员');
			$table->integer('wallet')->comment('之前的钱包金额');
			$table->integer('money')->comment('变动金额');
			$table->integer('type')->comment('类型 1充值 2提现 3分红4 返现5分销... ');
			$table->integer('created_at')->comment('创建时间');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ims_yz_member_money_record');
	}

}
