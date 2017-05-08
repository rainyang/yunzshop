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
		$this->down();
		
		Schema::create('yz_dispatch', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->nullable();
			$table->string('dispatch_name', 50)->nullable()->comment('配送模板名称');
			$table->integer('display_order')->nullable()->comment('排序');
			$table->boolean('enabled')->nullable()->comment('是否显示（1：是；0：否）');
			$table->boolean('is_default')->nullable()->comment('是否默认模板（1：是；0：否）');
			$table->boolean('calculate_type')->nullable()->comment('计费方式');
			$table->text('areas', 65535)->nullable()->comment('配送区域');
			$table->integer('first_weight')->nullable()->comment('首重克数');
			$table->integer('another_weight')->nullable()->comment('续重克数');
			$table->decimal('first_weight_price', 14)->nullable()->comment('首重价格');
			$table->decimal('another_weight_price', 14)->nullable()->comment('续重价格');
			$table->integer('first_piece')->nullable()->comment('首件个数');
			$table->integer('another_piece')->nullable()->comment('续件个数');
			$table->integer('first_piece_price')->nullable()->comment('首件价格');
			$table->integer('another_piece_price')->nullable()->comment('续件价格');
			$table->text('weight_data')->nullable()->comment('按重量计费数据');
			$table->text('piece_data')->nullable()->comment('按数量计费数据');
			$table->boolean('is_plugin')->nullable()->default(0);
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
		Schema::drop('ims_yz_dispatch');
	}

}
