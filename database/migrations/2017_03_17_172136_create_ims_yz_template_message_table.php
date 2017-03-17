<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzTemplateMessageTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_template_message', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('type', 20)->nullable()->default('system')->comment('模板类型 ');
			$table->string('item', 45)->comment('发送标识');
			$table->string('parent_item', 45)->default('')->comment('上级');
			$table->string('title', 45)->comment('标题');
			$table->string('template_id_short', 45)->comment('模板库中模板的编号');
			$table->string('template_id', 45)->comment('模板的ID');
			$table->string('content')->comment('详细内容');
			$table->string('example')->comment('内容示例');
			$table->boolean('status')->default(0)->comment('状态 1成功 0失败');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ims_yz_template_message');
	}

}
