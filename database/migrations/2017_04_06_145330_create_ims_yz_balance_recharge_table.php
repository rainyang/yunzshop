<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzBalanceRechargeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_balance_recharge', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('uniacid')->nullable();
			$table->integer('member_id')->nullable()->comment('会员ID');
			$table->decimal('old_money', 14)->nullable()->comment('充值前金额');
			$table->decimal('money', 14)->nullable()->comment('充值金额');
			$table->decimal('new_money', 14)->nullable()->comment('充值后金额');
			$table->integer('type')->nullable()->comment('充值类型（微信，支付宝）');
			$table->integer('created_at')->nullable()->comment('创建时间');
			$table->integer('updated_at')->nullable()->comment('修改时间');
			$table->string('ordersn', 50)->nullable()->comment('订单编号');
			$table->boolean('status')->nullable()->default(0)->comment('充值状态，-1充值失败，0正常，1充值成功');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ims_yz_balance_recharge');
	}

}
