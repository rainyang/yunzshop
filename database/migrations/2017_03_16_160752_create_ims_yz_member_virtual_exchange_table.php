<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzMemberVirtualExchangeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_member_virtual_exchange', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('uniacid');
			$table->integer('member_id')->comment('会员');
			$table->integer('from_type')->comment('初始类型');
			$table->integer('to_type')->comment('结果类型');
			$table->integer('from_value')->comment('初始值');
			$table->integer('to_value')->comment('结果值');
			$table->integer('rate')->comment('汇率');
			$table->integer('created_at')->comment('对换时间');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ims_yz_member_virtual_exchange');
	}

}
