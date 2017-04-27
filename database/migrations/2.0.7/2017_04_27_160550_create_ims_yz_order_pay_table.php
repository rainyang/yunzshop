<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzOrderPayTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_order_pay', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('pay_sn', 23)->default('')->comment('支付流水号');
			$table->boolean('status')->default(0)->comment('0未支付 1已支付 2已退款');
			$table->boolean('pay_type_id')->default(0)->comment('支付方式');
			$table->integer('pay_time')->default(0);
			$table->integer('refund_time')->default(0);
			$table->string('order_ids', 500)->default('')->comment('支付的订单id数组');
			$table->decimal('amount', 10)->default(0.00)->comment('金额');
			$table->integer('uid')->comment('用户id');
			$table->integer('updated_at')->nullable();
			$table->integer('created_at')->nullable();
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
		Schema::drop('ims_yz_order_pay');
	}

}
