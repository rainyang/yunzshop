<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzPayTypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_pay_type', function(Blueprint $table)
		{
			$table->increments('id')->comment('支付方式');
			$table->string('name', 50)->default('')->comment('名称');
			$table->integer('plugin_id')->comment('所属插件');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ims_yz_pay_type');
	}

}
