<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzCategoryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_category', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('uniacid')->nullable()->default(0)->index('idx_uniacid')->comment('所属帐号');
			$table->string('name', 50)->nullable()->comment('分类名称');
			$table->string('thumb')->nullable()->comment('分类图片');
			$table->integer('parent_id')->nullable()->default(0)->index('idx_parentid')->comment('上级分类ID,0为第一级');
			$table->string('description', 500)->nullable()->comment('分类介绍');
			$table->boolean('display_order')->nullable()->default(0)->index('idx_displayorder')->comment('排序');
			$table->boolean('enabled')->nullable()->default(1)->index('idx_enabled')->comment('是否开启');
			$table->boolean('is_home')->nullable()->default(0)->index('idx_ishome');
			$table->string('adv_img')->nullable()->default('');
			$table->string('adv_url', 500)->nullable()->default('');
			$table->boolean('level')->nullable()->default(0);
			$table->string('advimg_pc')->default('');
			$table->string('advurl_pc', 500)->default('');
			$table->integer('created_at')->nullable();
			$table->integer('updated_at')->nullable();
			$table->integer('deleted_at')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ims_yz_category');
	}

}
