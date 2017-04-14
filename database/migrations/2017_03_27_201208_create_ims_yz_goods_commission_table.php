<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzGoodsCommissionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_goods_commission', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('goods_id')->index('idx_good_id');
			$table->integer('is_commission')->nullable()->comment('是否参与分销');
			$table->boolean('show_commission_button')->default(0)->comment('显示"我要分销"按钮');
			$table->string('poster_picture')->nullable()->comment('海报图片');
			$table->boolean('has_commission')->nullable()->default(0)->comment('独立规则 1启用独立佣金比例');
			$table->integer('first_level_rate')->nullable()->comment('一级分销 独立比例');
			$table->decimal('first_level_pay', 14)->nullable()->comment('一级分销 独立固定金额');
			$table->integer('second_level_rate')->nullable()->comment('二级分销 独立比例');
			$table->decimal('second_level_pay', 14)->nullable()->comment('二级分销 独立固定金额');
			$table->integer('third_level_rate')->nullable()->comment('三级分销 独立比例');
			$table->decimal('third_level_pay', 14)->nullable()->comment('三级分销 独立固定金额');
			$table->integer('created_at')->nullable();
			$table->integer('updated_at')->nullable();
			$table->integer('deleted_at')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ims_yz_goods_commission');
	}

}
