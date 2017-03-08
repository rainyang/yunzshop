<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzMemberHistoryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_member_history', function(Blueprint $table)
		{
			$table->integer('id', true)->comment('主键');
			$table->integer('member_id')->comment('会员id');
			$table->integer('uniacid')->comment('所属公众号id');
			$table->integer('goods_id')->comment('商品id');
			$table->integer('created_at')->comment('创建时间');
			$table->integer('updated_at')->comment('修改时间，最后一次浏览时间');
			$table->integer('deleted_at')->nullable()->comment('删除时间，为空则未删除状态');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ims_yz_member_history');
	}

}
