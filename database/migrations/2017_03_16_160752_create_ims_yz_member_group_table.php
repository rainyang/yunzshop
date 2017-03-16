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
			$table->integer('created_at')->comment('创建时间');
			$table->integer('updated_at')->nullable()->comment('修改时间');
			$table->integer('deleted_at')->nullable()->comment('删除时间');
			$table->boolean('is_default')->default(0)->comment('是否默认，1为会员默认分组');
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
