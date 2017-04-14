<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzCommissionOrderGoodsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_commission_order_goods', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('commission_order_id')->nullable();
			$table->string('name')->nullable()->comment('商品名称');
			$table->string('thumb')->nullable()->comment('商品图片');
			$table->boolean('has_commission')->nullable()->comment('是否启用独立规则0:未启用1：启用');
			$table->integer('commission_rate')->nullable()->comment('独立比例');
			$table->decimal('commission_pay', 14)->nullable()->comment('独立固定金额');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ims_yz_commission_order_goods');
	}

}
