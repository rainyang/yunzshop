<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzMemberVirtualOutTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_member_virtual_out', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('uniacid')->comment('统一公众号');
			$table->integer('member_id')->comment('会员');
			$table->boolean('type')->comment('类型：1积分2云币');
			$table->integer('value')->comment('变动积分');
			$table->integer('created_at')->comment('创建时间');
			$table->boolean('status')->comment('状态');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ims_yz_member_virtual_out');
	}

}
