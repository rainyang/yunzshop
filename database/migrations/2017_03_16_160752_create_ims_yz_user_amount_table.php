<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzUserAmountTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_user_amount', function(Blueprint $table)
		{
			$table->integer('id')->primary()->comment('编号');
			$table->integer('uniacid')->comment('统一公众号');
			$table->integer('member_id')->comment('用户ID');
			$table->integer('money')->comment('金额');
			$table->integer('created_at')->default(0)->comment('创建时间');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ims_yz_user_amount');
	}

}
