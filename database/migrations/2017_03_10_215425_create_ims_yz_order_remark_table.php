<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzOrderRemarkTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_order_remark', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('order_id')->index('idx_order_id');
			$table->char('remark')->comment('买家备注');
			$table->integer('updated_at')->default(0);
			$table->integer('created_at')->default(0);
			$table->integer('deleted_at')->default(0);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('yz_order_remark');
	}

}
