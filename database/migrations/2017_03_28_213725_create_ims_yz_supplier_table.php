<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzSupplierTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_supplier', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('member_id')->default(0)->comment('会员id');
			$table->string('username', 50)->default('0')->comment('账号');
			$table->string('password', 50)->default('0')->comment('密码');
			$table->string('realname', 50)->default('0')->comment('姓名');
			$table->string('mobile', 50)->default('0')->comment('电话');
			$table->boolean('status')->default(0)->comment('0 = 申请状态 1 = 通过   -1 = 驳回');
			$table->integer('apply_time')->default(0)->comment('申请时间');
			$table->integer('created_at')->default(0);
			$table->integer('updated_at')->default(0);
			$table->integer('deleted_at')->default(0);
			$table->integer('uniacid')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ims_yz_supplier');
	}

}
