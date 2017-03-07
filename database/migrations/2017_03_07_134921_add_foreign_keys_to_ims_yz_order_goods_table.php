<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToImsYzOrderGoodsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('yz_order_goods', function(Blueprint $table)
		{
			$table->foreign('goods_id', 'goods_id')->references('id')->on('ims_yz_goods')->onUpdate('CASCADE')->onDelete('NO ACTION');
			$table->foreign('order_id', 'order_id')->references('id')->on('ims_yz_order')->onUpdate('CASCADE')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('yz_order_goods', function(Blueprint $table)
		{
			$table->dropForeign('goods_id');
			$table->dropForeign('order_id');
		});
	}

}
