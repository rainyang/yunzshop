<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzSupplierGoodsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_supplier_goods', function(Blueprint $table)
		{
			$table->integer('id');
			$table->integer('goods_id')->default(0)->comment('商品id');
			$table->integer('supplier_id')->default(0)->comment('供应商id');
			$table->integer('member_id')->default(0)->comment('会员id');
			$table->integer('created_at')->default(0);
			$table->integer('update_at')->default(0);
			$table->integer('deleted_at')->default(0);
			$table->integer('uniacid')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ims_yz_supplier_goods');
	}

}
