<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzDesignerMenuTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_designer_menu', function(Blueprint $table)
		{
			$table->integer('id', true)->comment('id');
			$table->integer('uniacid')->nullable()->default(0)->index('idx_uniacid')->comment('公众号ID');
			$table->string('menu_name')->nullable()->comment('菜单名称');
			$table->boolean('is_default')->nullable()->default(0)->index('idx_isdefault')->comment('是否默认');
			$table->integer('created_at')->nullable()->default(0)->index('idx_createtime')->comment('创建时间');
			$table->text('menus', 65535)->nullable()->comment('菜单');
			$table->text('params', 65535)->nullable()->comment('参数');
			$table->integer('updated_at')->nullable()->comment('修改时间');
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
		Schema::drop('ims_yz_designer_menu');
	}

}
