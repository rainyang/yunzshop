<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzSettingTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_setting', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('unique_account_id')->comment('统一账号');
			$table->string('group')->default('shop')->comment('分组');
			$table->string('key')->comment('配置key名');
			$table->string('type')->comment('值类型');
			$table->text('value', 65535)->comment('值');
			$table->integer('uniacid')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ims_yz_setting');
	}

}
