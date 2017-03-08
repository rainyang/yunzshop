<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzDispatchTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_dispatch', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('uniacid')->nullable()->default(0)->comment('公众号id');
			$table->string('dispatch_name', 50)->nullable()->default('')->comment('配送模板名称');
			$table->integer('display_order')->nullable()->default(0)->comment('排序');
			$table->integer('first_weight_price')->unsigned()->nullable()->default(0)->comment('首重价格');
			$table->decimal('another_weight_price', 11, 0)->nullable()->default(0)->comment('续重价格');
			$table->integer('first_weight')->nullable()->default(0)->comment('首重克数');
			$table->integer('another_weight')->nullable()->default(0)->comment('续重克数');
			$table->text('areas', 65535)->nullable()->comment('配送区域');
			$table->text('carriers', 65535)->nullable();
			$table->boolean('enabled')->nullable()->default(0)->comment('是否显示（1：是；0：否）');
			$table->boolean('is_default')->nullable()->default(0)->comment('是否默认模板（1：是；0：否）');
			$table->boolean('calculate_type')->nullable()->default(0)->comment('计费方式');
			$table->integer('first_piece_price')->nullable()->default(0)->comment('首件价格');
			$table->integer('another_piece_price')->nullable()->default(0)->comment('续件价格');
			$table->integer('first_piece')->nullable()->default(0)->comment('首件个数');
			$table->integer('another_piece')->nullable()->default(0)->comment('续件个数');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ims_yz_dispatch');
	}

}
