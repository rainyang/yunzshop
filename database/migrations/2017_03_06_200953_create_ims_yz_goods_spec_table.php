<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzGoodsSpecTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_goods_spec', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('uniacid')->nullable()->default(0)->index('idx_uniacid')->comment('公众号ID');
			$table->integer('goods_id')->nullable()->default(0)->index('idx_goodsid')->comment('商品ID');
			$table->string('title', 50)->nullable()->comment('标题');
			$table->string('description', 1000)->nullable()->comment('介绍');
			$table->boolean('display_type')->nullable()->default(0)->comment('显示类型');
			$table->text('content', 65535)->nullable()->comment('内容');
			$table->integer('display_order')->nullable()->default(0)->index('idx_displayorder')->comment('排序');
			$table->string('propId')->nullable()->comment('淘宝插件');
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
		Schema::drop('ims_yz_goods_spec');
	}

}
