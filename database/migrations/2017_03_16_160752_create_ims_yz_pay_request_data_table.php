<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzPayRequestDataTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_pay_request_data', function(Blueprint $table)
		{
			$table->integer('id')->primary()->comment('编号');
			$table->integer('uniacid')->comment('统一公众号');
			$table->integer('order_id')->comment('支付单ID/提现单ID/退款单ID');
			$table->boolean('type')->comment('支付种类 1-订单支付 2-充值  ');
			$table->boolean('third_type')->nullable()->comment('支付类型 1-微信；2-支付宝；3-余额');
			$table->text('params', 65535)->comment('请求数据');
			$table->integer('created_at')->default(0)->comment('创建时间');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ims_yz_pay_request_data');
	}

}
