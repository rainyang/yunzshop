<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzQqConfigTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_qq_config', function(Blueprint $table)
		{
			$table->integer('config_id')->primary();
			$table->integer('uniacid')->default(0)->comment('统一公众号ID');
			$table->string('app_key', 64)->comment('应用公钥');
			$table->string('app_secret', 64)->comment('应用私钥');
			$table->boolean('type')->default(0)->comment('接入类型 0-网站；1-移动');
			$table->integer('created_at')->unsigned()->default(0)->comment('创建时间');
			$table->integer('updated_at')->unsigned()->default(0);
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
		Schema::drop('ims_yz_qq_config');
	}

}
