<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzPayLogTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_pay_log', function(Blueprint $table)
		{
			$table->integer('id', true)->comment('编号');
			$table->integer('uniacid')->comment('统一公众号');
			$table->integer('member_id')->comment('用户ID');
			$table->boolean('type')->comment('支付种类 1-订单 2-充值 3-提现 4-退款');
			$table->boolean('third_type')->comment('支付方式 ');
			$table->integer('price');
			$table->text('operation', 65535)->comment(' 操作内容');
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
		Schema::drop('ims_yz_pay_log');
	}

}
