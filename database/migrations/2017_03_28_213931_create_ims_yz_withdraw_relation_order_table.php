<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzWithdrawRelationOrderTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_withdraw_relation_order', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('withdraw_id')->nullable()->default(0);
			$table->integer('order_id')->nullable()->default(0);
			$table->integer('created_at')->nullable()->default(0);
			$table->integer('updated_at')->nullable()->default(0);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ims_yz_withdraw_relation_order');
	}

}
