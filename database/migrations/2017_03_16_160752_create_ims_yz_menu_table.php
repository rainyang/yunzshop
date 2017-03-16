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
		Schema::create('yz_menu', function(Blueprint $table)
		{
			$table->integer('id', true)->comment('编号');
			$table->integer('parent_id')->comment('菜单父ID');
			$table->string('item', 45)->default('')->comment('菜单标签');
			$table->string('name', 45)->comment('菜单名称');
			$table->text('url', 65535)->comment('url  格式:http或路由');
			$table->text('url_params', 65535)->comment('url参数 url是路由则启用 否则不启用');
			$table->boolean('permit')->default(0)->comment('是否设置权限检测 0-否；1-是');
			$table->boolean('menu')->default(0)->comment('是否显示菜单 0-不显示；1-显示');
			$table->string('icon', 45)->comment('菜单图标');
			$table->integer('sort')->default(0)->comment('排序');
			$table->integer('created_at')->default(0)->comment('创建时间');
			$table->integer('updated_at')->default(0)->comment('更新时间');
			$table->integer('deleted_at')->default(0)->comment('删除时间');
			$table->integer('status')->default(0);
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
