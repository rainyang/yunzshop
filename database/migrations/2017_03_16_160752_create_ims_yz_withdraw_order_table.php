<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzWithdrawOrderTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_withdraw_order', function(Blueprint $table)
		{
			$table->integer('id', true)->comment('编号');
			$table->integer('uniacid')->comment('统一公众号');
			$table->integer('member_id')->comment('用户ID');
			$table->string('int_order_no', 20)->nullable()->comment('提现单号');
			$table->string('out_order_no', 20)->comment('提现订单号');
			$table->string('status', 45)->comment('提现状态 -1 无效 0 未知 1 正在申请 2 审核通过 3 已经打款');
			$table->integer('price')->comment('提现金额');
			$table->boolean('type')->comment('提现方式 0-余额；1-微信钱包；2-微信红包；3-支付宝');
			$table->integer('created_at')->default(0)->comment('创建时间');
			$table->integer('updated_at')->default(0)->comment('更新时间');
			$table->integer('deleted_at')->nullable()->comment('删除时间');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ims_yz_withdraw_order');
	}

}
