<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzMemberLevelTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_member_level', function(Blueprint $table)
		{
			$table->integer('id', true)->comment('主键');
			$table->integer('uniacid')->comment('所属公众号');
			$table->integer('level')->comment('等级权重，数值越大权重越高');
			$table->string('level_name', 45)->comment('会员等级名称');
			$table->string('order_money', 45)->nullable()->comment('升级条件，订单满足金额值');
			$table->string('order_count', 45)->nullable()->comment('升级条件，满足订单数量值');
			$table->integer('goods_id')->nullable()->comment('升级条件，购买指定商品升级');
			$table->string('discount', 45)->nullable()->comment('等级享受折扣');
			$table->integer('created_at');
			$table->integer('updated_at');
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
		Schema::drop('ims_yz_member_level');
	}

}
