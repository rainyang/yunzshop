<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzGoodsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_goods', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('uniacid')->nullable()->default(0)->index('idx_uniacid');
			$table->integer('category_id')->nullable()->comment('分类id');
			$table->integer('brand_id')->nullable();
			$table->boolean('type')->nullable()->default(1)->comment('1为实体，2为虚拟');
			$table->boolean('status')->nullable()->default(1)->comment('状态 1上架，0下架');
			$table->integer('display_order')->nullable()->default(0);
			$table->string('title', 100)->nullable()->default('')->comment('商品名称');
			$table->string('thumb')->nullable()->default('')->comment('商品图');
			$table->text('thumb_url', 65535)->nullable()->comment('缩略图地址');
			$table->string('sku', 5)->nullable()->default('')->comment('商品单位 unit');
			$table->string('description', 1000)->nullable()->default('')->comment('分享描述');
			$table->text('content', 65535)->nullable()->comment('商品详情');
			$table->string('goods_sn', 50)->nullable()->default('')->comment('商品编号');
			$table->string('product_sn', 50)->nullable()->default('')->comment('商品条码');
			$table->decimal('market_price', 10)->nullable()->default(0.00)->comment('原价');
			$table->decimal('price', 10)->nullable()->default(0.00)->comment('商品现价');
			$table->decimal('cost_price', 10)->nullable()->default(0.00)->comment('成本价');
			$table->integer('stock')->nullable()->default(0)->comment('商品库存 原total');
			$table->integer('reduce_stock_method')->nullable()->default(0)->comment('减库存方式 0 拍下减库存 1 付款减库存 2 永久不减  totalcnf');
			$table->integer('show_sales')->nullable()->default(0)->comment('已出售数量');
			$table->integer('real_sales')->nullable()->default(0)->comment('实际出售数量');
			$table->decimal('weight', 10)->nullable()->default(0.00)->comment('重量');
			$table->integer('has_option')->nullable()->default(0)->comment('启用商品规格 0 不启用 1 启用
启用商品规格 0 不启用 1 启用');
			$table->boolean('is_new')->nullable()->default(0)->index('idx_isnew')->comment('新上');
			$table->boolean('is_hot')->nullable()->default(0)->index('idx_ishot')->comment('热卖');
			$table->boolean('is_discount')->nullable()->default(0)->index('idx_isdiscount')->comment('促销');
			$table->boolean('is_recommand')->nullable()->default(0)->index('idx_isrecommand')->comment('推荐');
			$table->boolean('is_comment')->nullable()->default(0)->index('idx_iscomment')->comment('允许评论');
			$table->boolean('is_deleted')->default(0)->index('idx_deleted')->comment('是否删除');
			$table->integer('created_at')->nullable()->comment('建立时间');
			$table->integer('deleted_at')->nullable();
			$table->integer('updated_at')->nullable()->comment('更新时间');
            $table->integer('is_plugin')->unsigned()->default(0)->comment('0为自营');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ims_yz_goods');
	}

}
