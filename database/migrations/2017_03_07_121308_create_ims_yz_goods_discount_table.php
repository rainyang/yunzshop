<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzGoodsDiscountTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_goods_discount', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('goods_id')->index('idx_goodid')->comment('商品id');
			$table->boolean('level_discount_type')->comment('等级折扣类型（1：会员等级；2：分销商等级；）');
			$table->boolean('discount_method')->comment('折扣方式（1：折扣；2：固定金额）');
			$table->integer('level_id')->comment('会员等级id');
			$table->decimal('discount_value', 3)->comment('具体折扣数值 ');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ims_yz_goods_discount');
	}

}
