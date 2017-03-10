<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzMenuTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
	    Schema::dropIfExists('yz_menu');
		Schema::create('yz_menu', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('name', 45)->comment('名称');
			$table->string('item', 45)->comment('标识');
			$table->string('url')->default('')->comment('路由或链接地址');
			$table->string('url_params')->default('')->comment('路由参数');
			$table->boolean('permit')->default(0)->comment('权限控制 1是 0否');
			$table->boolean('menu')->default(0)->comment('菜单显示 1是 0否');
			$table->string('icon', 45)->default('')->comment('图标');
			$table->integer('parent_id')->default(0)->comment('上级');
			$table->integer('sort')->default(0)->comment('排序');
			$table->boolean('status')->default(0)->comment('状态 1启用 0禁用');
			$table->integer('created_at')->default(0)->comment('创建时间');
			$table->integer('updated_at')->default(0)->comment('更新时间');
			$table->integer('deleted_at')->nullable()->comment('删除时间');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ims_yz_menu');
	}

}
