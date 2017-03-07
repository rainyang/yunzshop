<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzGoodsDispatchTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_goods_dispatch', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('goods_id')->index('idx_good_id');
			$table->boolean('dispatch_type')->default(1)->comment('运费设置  1:统一运费 2:运费模板');
			$table->integer('dispatch_price')->nullable()->default(0)->comment('统一运费金额');
			$table->integer('dispatch_id')->nullable()->comment('运费模板ID');
			$table->boolean('is_cod')->default(1)->comment('是否支持货到付款 1:不支持2：支持');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ims_yz_goods_dispatch');
	}

}
