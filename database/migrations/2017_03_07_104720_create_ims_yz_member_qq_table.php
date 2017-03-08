<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzMemberQqTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_member_qq', function(Blueprint $table)
		{
			$table->integer('qq_id', true);
			$table->integer('uniacid')->comment('统一公众号ID');
			$table->integer('member_id');
			$table->string('nickname')->comment('昵称');
			$table->string('figureurl')->comment('大小为30×30像素的QQ空间头像URL');
			$table->string('figureurl_1')->comment('大小为50×50像素的QQ空间头像URL');
			$table->string('figureurl_2')->comment('大小为100×100像素的QQ空间头像URL。');
			$table->string('figureurl_qq_1')->comment('大小为40×40像素的QQ头像URL。');
			$table->string('figureurl_qq_2')->comment('大小为100×100像素的QQ头像URL。需要注意，不是所有的用户都拥有QQ的100x100的头像，但40x40像素则是一定会有。');
			$table->boolean('gender')->default(0)->comment('性别 0-男；1-女');
			$table->string('is_yellow_year_vip', 45)->default('0')->comment('标识用户是否为黄钻用户 0- 不是；1-是');
			$table->integer('vip')->default(0)->comment('标识用户是否为黄钻用户0－不是；1－是');
			$table->boolean('yellow_vip_level')->default(0)->comment('黄钻等级');
			$table->boolean('level')->default(0)->comment('黄钻等级');
			$table->boolean('is_yellow_vip')->default(0)->comment('识是否为年费黄钻用户 0－不是； 1－是');
			$table->integer('created_at')->unsigned()->default(0);
			$table->integer('updated_at')->unsigned()->default(0);
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
		Schema::drop('ims_yz_member_qq');
	}

}
