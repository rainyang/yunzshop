<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzStreetTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_street', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('areaname')->nullable();
			$table->integer('parentid')->nullable();
			$table->integer('level')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ims_yz_street');
	}

}
