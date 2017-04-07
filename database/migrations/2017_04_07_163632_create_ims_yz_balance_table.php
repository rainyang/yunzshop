<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzBalanceTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_balance', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('uniacid')->nullable();
			$table->integer('member_id')->nullable();
			$table->decimal('old_money', 14)->nullable()->comment('原金额值');
			$table->decimal('change_money', 14)->comment('改变金额值');
			$table->decimal('new_money', 14)->comment('改变后金额');
			$table->boolean('type')->comment('1收入，2支出');
			$table->boolean('service_type')->comment('业务类型【1充值，2消费，3转账，4抵扣，5奖励，6余额提现，7提现至余额，8抵扣取消回滚，9奖励取消回滚】');
			$table->string('serial_number', 45)->default('')->comment('流水号、订单号');
			$table->integer('operator')->comment('-2,会员，-1,订单，0(商城)，1++（插件）');
			$table->string('operator_id', 45)->default('')->comment('关联ID值，如文章营销的某文章ID');
			$table->string('remark', 200)->default('')->comment('备注【余额详细余额好】');
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
		Schema::drop('ims_yz_balance');
	}

}
