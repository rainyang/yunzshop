<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzGoodsSaleTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_goods_sale', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('goods_id')->index('idx_good_id');
			$table->integer('love_money')->nullable()->default(0)->comment('爱心基金');
			$table->integer('max_point_deduct')->nullable()->default(0)->comment('积分抵扣 最多抵扣');
			$table->integer('max_balance_deduct')->nullable()->default(0)->comment('余额抵扣 最多抵扣');
			$table->integer('is_sendfree')->nullable()->default(0)->comment('是否包邮');
			$table->integer('ed_num')->nullable()->default(0)->comment('单品满件包邮 件数');
			$table->integer('ed_money')->nullable()->default(0)->comment('单品满额包邮 金额');
			$table->text('ed_areas', 65535)->nullable()->comment('不参与单品包邮地区');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ims_yz_goods_sale');
	}

}
