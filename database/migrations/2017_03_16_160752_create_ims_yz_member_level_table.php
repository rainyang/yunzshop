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
			$table->integer('order_money')->nullable()->comment('升级条件，订单满足金额值');
			$table->integer('order_count')->nullable()->comment('升级条件，满足订单数量值');
			$table->integer('goods_id')->nullable()->comment('升级条件，购买指定商品升级');
			$table->string('discount', 45)->nullable()->comment('等级享受折扣');
			$table->integer('creted_at')->nullable();
			$table->integer('updated_at')->nullable();
			$table->integer('deleted_at')->nullable();
			$table->boolean('is_default')->default(0)->comment('是否默认，1为默认会员等级');
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
