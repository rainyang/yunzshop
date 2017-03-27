<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzAgentLevelTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_agent_level', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->default(0);
			$table->string('name', 100)->default('')->comment('分销等级名称');
			$table->integer('level')->default(0)->comment('权重');
			$table->integer('first_level')->nullable()->default(0)->comment('一级分比例');
			$table->integer('second_level')->nullable()->default(0)->comment('二级分销比例');
			$table->integer('third_level')->nullable()->default(0)->comment('三级分销比例');
			$table->text('upgraded', 65535)->nullable()->comment('升级条件');
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
		Schema::drop('ims_yz_agent_level');
	}

}
