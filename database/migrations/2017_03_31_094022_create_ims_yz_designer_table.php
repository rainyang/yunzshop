<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzDesignerTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_designer', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('uniacid')->default(0)->index('idx_uniacid')->comment('公众号');
			$table->string('page_name')->default('')->comment('页面名称');
			$table->boolean('page_type')->default(0)->index('idx_pagetype')->comment('页面类型');
			$table->text('page_info', 65535)->comment('页面信息');
			$table->string('keyword')->nullable()->default('')->index('idx_keyword')->comment('关键字');
			$table->boolean('is_default')->default(0)->comment('默认页面');
			$table->text('datas');
			$table->integer('created_at')->comment('页面创建时间');
			$table->integer('updated_at')->comment('页面最后保存时间');
			$table->integer('deleted_at')->nullable()->comment('页面删除时间');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ims_yz_designer');
	}

}
