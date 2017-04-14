<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzPermissionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_permission', function(Blueprint $table)
		{
			$table->increments('id');
			$table->boolean('type')->comment('1:user 2:role 3:account');
			$table->integer('item_id')->comment('目标ID:user_id role_id uniacid');
			$table->string('permission')->comment('权限值');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ims_yz_permission');
	}

}
