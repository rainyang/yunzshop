<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzGoodsShareTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_goods_share', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('goods_id')->index('idx_goodid')->comment('商品id');
			$table->boolean('need_follow')->nullable()->comment('强制关注');
			$table->string('no_follow_message')->nullable()->default('')->comment('未关注提示消息');
			$table->string('follow_message')->nullable()->default('')->comment('关注引导信息');
			$table->string('share_title', 50)->nullable()->default('')->comment('分享标题');
			$table->string('share_thumb')->nullable()->default('')->comment('分享图片');
			$table->string('share_desc')->nullable()->default('')->comment('分享描述');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ims_yz_goods_share');
	}

}
