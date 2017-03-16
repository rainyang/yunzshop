<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzMemberVirtualRecordTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_member_virtual_record', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('uniacid')->comment('统一公众号');
			$table->integer('member_id')->comment('会员');
			$table->integer('wallet')->comment('之前的');
			$table->integer('value')->comment('变动值');
			$table->integer('type')->comment('类型 1进2出 ');
			$table->integer('created_at')->comment('创建时间');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ims_yz_member_virtual_record');
	}

}
