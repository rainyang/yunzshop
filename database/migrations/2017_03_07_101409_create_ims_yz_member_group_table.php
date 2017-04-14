<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzMemberGroupTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_member_group', function(Blueprint $table)
		{
			$table->integer('id', true)->comment('主键');
			$table->integer('uniacid')->comment('所属公众号');
			$table->string('group_name', 45)->comment('分组名称');
			$table->integer('created_at');
			$table->integer('updated_at');
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
		Schema::drop('ims_yz_member_group');
	}

}
