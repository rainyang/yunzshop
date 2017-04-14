<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzMemberCartTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_member_cart', function(Blueprint $table)
		{
			$table->integer('id', true)->comment('主键');
			$table->integer('member_id')->comment('会员id');
			$table->integer('uniacid')->comment('所属公众号id');
			$table->integer('goods_id')->comment('商品id');
			$table->integer('total')->comment('加入购物车数量');
			$table->integer('price')->comment('销售价格');
			$table->integer('option_id')->comment('商品规格id');
			$table->integer('created_at')->comment('加入购物车时间');
			$table->integer('updated_at')->comment('最后一次修改时间');
			$table->integer('deleted_at')->nullable()->comment('移除购物车时间');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ims_yz_member_cart');
	}

}
