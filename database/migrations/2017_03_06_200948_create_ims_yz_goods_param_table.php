<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzGoodsParamTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_goods_param', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('uniacid')->nullable()->default(0)->index('idx_uniacid');
			$table->integer('goods_id')->nullable()->default(0)->index('idx_goodsid');
			$table->string('title', 50)->nullable()->comment('标题');
			$table->text('value', 65535)->nullable()->comment('值');
			$table->integer('displayorder')->nullable()->default(0)->index('idx_displayorder')->comment('排序');
			$table->integer('updated_at')->nullable();
			$table->integer('created_at')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ims_yz_goods_param');
	}

}
