<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzDispatchTypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_dispatch_type', function(Blueprint $table)
		{
			$table->increments('id')->comment('配送方式');
			$table->string('name', 50)->default('')->comment('名称');
			$table->integer('plugin')->comment('所属插件');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ims_yz_dispatch_type');
	}

}
