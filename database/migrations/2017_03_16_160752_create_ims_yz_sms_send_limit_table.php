<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzSmsSendLimitTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_sms_send_limit', function(Blueprint $table)
		{
			$table->integer('sms_id', true)->comment('编号');
			$table->integer('uniacid')->comment('统一公众号');
			$table->string('mobile', 11)->comment('手机号');
			$table->boolean('total')->comment('发送数量');
			$table->integer('created_at')->comment('短信发送时间');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ims_yz_sms_send_limit');
	}

}
