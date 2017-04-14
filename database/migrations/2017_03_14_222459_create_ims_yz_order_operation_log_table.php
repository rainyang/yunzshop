<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzOrderOperationLogTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_order_operation_log', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('order_id')->nullable()->default(0)->comment('订单id');
			$table->boolean('before_operation_status')->nullable()->default(0)->comment('操作前订单状态');
			$table->boolean('after_operation_status')->nullable()->default(0)->comment('操作后订单状态');
			$table->string('operator', 50)->nullable()->default('')->comment('操作人');
			$table->integer('operation_time')->nullable()->default(0)->comment('操作时间');
			$table->integer('created_at')->nullable()->default(0);
			$table->integer('updated_at')->nullable();
			$table->string('type', 10)->nullable()->default('0');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ims_yz_order_operation_log');
	}

}
