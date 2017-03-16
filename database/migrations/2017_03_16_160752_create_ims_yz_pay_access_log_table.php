<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzPayAccessLogTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_pay_access_log', function(Blueprint $table)
		{
			$table->integer('id', true)->comment('编号');
			$table->integer('uniacid')->comment('统一公众号');
			$table->integer('member_id')->comment('用户ID');
			$table->text('url', 65535)->comment('访问地址');
			$table->char('http_method', 7)->comment('HTTP数据传输方法');
			$table->string('ip', 135)->comment('远程客户端的IP主机地址');
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
		Schema::drop('ims_yz_pay_access_log');
	}

}
