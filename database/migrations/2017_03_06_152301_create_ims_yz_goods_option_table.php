<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzGoodsOptionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_goods_option', function(Blueprint $table)
		{
			$table->integer('id', true)->comment('规格ID');
			$table->integer('uniacid')->nullable()->default(0)->index('idx_uniacid')->comment('所属公众号ID');
			$table->integer('goods_id')->nullable()->default(0)->index('idx_goodsid')->comment('所属商品ID');
			$table->string('title', 50)->nullable()->comment('规格名称');
			$table->string('thumb', 60)->nullable()->comment('此规格展示图片');
			$table->decimal('product_price', 10)->nullable()->default(0.00)->comment('此规格原件');
			$table->decimal('market_price', 10)->nullable()->default(0.00)->comment('此规格现价');
			$table->decimal('cost_price', 10)->nullable()->default(0.00)->comment('此规格成本');
			$table->integer('stock')->nullable()->default(0)->comment('此规格库存');
			$table->decimal('weight', 10)->nullable()->default(0.00);
			$table->integer('display_order')->nullable()->default(0)->index('idx_displayorder');
			$table->text('specs', 65535)->nullable()->comment('规格项ID组合编号');
			$table->string('skuId')->nullable()->default('');
			$table->string('goods_sn')->nullable()->default('');
			$table->string('product_sn')->nullable()->default('');
			$table->integer('virtual')->nullable()->default(0);
			$table->string('red_price', 50)->nullable()->default('');
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
		Schema::drop('ims_yz_goods_option');
	}

}
