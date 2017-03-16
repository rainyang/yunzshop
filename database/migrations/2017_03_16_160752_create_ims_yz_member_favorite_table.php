<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzMemberFavoriteTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_member_favorite', function(Blueprint $table)
		{
			$table->integer('id', true)->comment('主键');
			$table->integer('member_id')->comment('会员id');
			$table->integer('uniacid')->comment('所属公众号id');
			$table->integer('goods_id')->comment('商品id');
			$table->integer('created_at')->comment('创建时间');
			$table->boolean('deleted_at')->nullable()->comment('是否删除，0正常状态，1删除状态');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ims_yz_member_favorite');
	}

}
