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
			$table->string('title', 50)->nullable()->comment('拼接规格名称，冗余');
			$table->string('thumb', 60)->nullable()->comment('此规格展示图片');
			$table->integer('product_price')->nullable()->default(0)->comment('此规格现价');
			$table->integer('market_price')->nullable()->default(0)->comment('此规格原价');
			$table->integer('cost_price')->nullable()->default(0)->comment('此规格成本');
			$table->integer('stock')->nullable()->default(0)->comment('此规格库存');
			$table->decimal('weight', 10)->nullable()->default(0.00)->comment('重量');
			$table->integer('display_order')->nullable()->default(0)->index('idx_displayorder')->comment('排序');
			$table->text('specs', 65535)->nullable()->comment('规格项ID组合编号');
			$table->string('skuId')->nullable()->default('');
			$table->string('goods_sn')->nullable()->default('')->comment('商品编码');
			$table->string('product_sn')->nullable()->default('')->comment('产品编码');
			$table->integer('virtual')->nullable()->default(0);
			$table->string('red_price', 50)->nullable()->default('')->comment('红包价格');
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
