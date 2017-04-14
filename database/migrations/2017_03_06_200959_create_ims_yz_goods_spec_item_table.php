<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzGoodsSpecItemTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_goods_spec_item', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('uniacid')->nullable()->default(0)->index('idx_uniacid')->comment('公众号ID');
			$table->integer('specid')->nullable()->default(0)->index('idx_specid')->comment('规格ID');
			$table->string('title')->nullable()->comment('标题');
			$table->string('thumb')->nullable()->comment('图片');
			$table->integer('show')->nullable()->default(0)->index('idx_show')->comment('显示');
			$table->integer('display_order')->nullable()->default(0)->index('idx_displayorder')->comment('排序');
			$table->string('valueId')->nullable()->comment('淘宝插件');
			$table->integer('virtual')->nullable()->default(0)->comment('虚拟物品');
			$table->integer('created_at')->nullable();
			$table->integer('deleted_at')->nullable();
			$table->integer('updated_at')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ims_yz_goods_spec_item');
	}

}
