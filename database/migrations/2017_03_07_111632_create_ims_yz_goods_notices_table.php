<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzGoodsNoticesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_goods_notices', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('goods_id')->index('idx_good_id');
			$table->integer('uid')->nullable()->comment('商家通知 uid');
			$table->boolean('type')->nullable()->comment('通知方式 0:下单通知1：付款通知2:买家收货通知');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ims_yz_goods_notices');
	}

}
