<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzOrderMappingTable extends Migration {

	/**
	 * 在订单数据迁移时, 记录新旧order_id的对应关系
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_order_mapping', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('old_order_id')->comment('旧商城的订单ID');
			$table->integer('new_order_id')->comment('重构商城对应的订单ID');
			$table->char('old_openid', 50)->comment('旧商城的用户openid');
			$table->integer('new_member_id')->comment('重构商城对应的member_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('yz_order_mapping');
	}

}
