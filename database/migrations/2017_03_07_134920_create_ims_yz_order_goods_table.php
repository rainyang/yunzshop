<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzOrderGoodsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_order_goods', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('uniacid')->default(0)->comment('公众号id');
			$table->integer('order_id')->default(0)->index('order_id')->comment('订单ID');
			$table->integer('goods_id')->default(0)->index('goods_id')->comment('商品ID');
			$table->decimal('goods_price', 10)->default(0.00)->comment('商品快照价格');
			$table->integer('total')->default(1)->comment('订单商品件数');
			$table->integer('create_time')->default(0)->comment('创建时间');
			$table->integer('price')->default(0)->comment('真实价格');
			$table->string('goods_sn', 50)->default('')->comment('商品编码');
			$table->integer('member_id')->default(0)->comment('会员身份标识');
			$table->string('thumb', 50)->default('');
			$table->string('title', 50)->default('');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ims_yz_order_goods');
	}

}
