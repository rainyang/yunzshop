<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzRoleTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_role', function(Blueprint $table)
		{
			$table->integer('id')->unsigned()->primary()->comment('自增ID');
			$table->integer('uniacid')->comment('统一账号');
			$table->string('name', 45)->comment('名称');
			$table->integer('created_at')->default(0)->comment('创建时间');
			$table->integer('updated_at')->default(0)->comment('更新时间');
			$table->integer('deleted_at')->nullable()->comment('删除时间');
			$table->boolean('status')->default(0)->comment('状态');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ims_yz_role');
	}

}
