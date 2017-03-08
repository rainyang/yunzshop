<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzMemberRelationTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_member_relation', function(Blueprint $table)
		{
			$table->integer('id', true)->comment('编号');
			$table->integer('uniacid')->nullable();
			$table->boolean('status')->default(0)->comment('是否启用关系链 0-关闭；1-开启');
			$table->boolean('become')->default(0)->comment('成为分销商条件 0-无条件；1-申请；2-消费x次；3-消费x元；4-购买商品');
			$table->boolean('become_order')->default(0)->comment('消费条件统计的方式 0-付款后；1-完成后');
			$table->boolean('become_child')->default(0)->comment('成为下线条件 0-分享链接；1-首次下单；2-首次付款');
			$table->integer('become_ordercount')->nullable()->default(0)->comment('消费x次');
			$table->decimal('become_moneycount', 5)->nullable()->default(0.00)->comment('消费x元');
			$table->integer('become_goods_id')->nullable()->default(0)->comment('购买的商品');
			$table->boolean('become_info')->default(1)->comment('完善信息 0-不需要；1-需要');
			$table->boolean('become_check')->default(1)->comment('成为分销商是否需要审核 0-不需要；1-需要');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ims_yz_member_relation');
	}

}
