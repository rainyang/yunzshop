<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzTemplateMessageRecordTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_template_message_record', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('uniacid')->nullable()->comment('统一账号');
			$table->string('member_id', 20)->comment('会员');
			$table->char('openid', 32)->default('')->comment('接收用户openid');
			$table->string('template_id', 45)->comment('模板ID');
			$table->string('url')->default('')->comment('跳转URL');
			$table->char('top_color', 7)->default('')->comment('全局颜色');
			$table->text('data', 65535)->comment('数据');
			$table->integer('created_at')->default(0)->comment('创建时间');
			$table->integer('updated_at')->default(0)->comment('更新时间');
			$table->integer('send_time')->default(0)->comment('发送时间');
			$table->boolean('status')->default(0)->comment('状态 0未发送 1发送中  2发送失败 3发送成功 ');
			$table->string('msgid', 20)->nullable()->default('')->comment('微信消息id');
			$table->boolean('result')->default(0)->comment('发送结果');
			$table->integer('wechat_send_at')->default(0)->comment('微信发送时间');
			$table->boolean('sended_count')->default(1)->comment('发送次数');
			$table->text('extend_data', 65535)->nullable()->comment('扩展数据');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ims_yz_template_message_record');
	}

}
