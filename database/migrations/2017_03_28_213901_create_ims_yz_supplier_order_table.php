<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzSupplierOrderTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_supplier_order', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('order_id')->default(0)->comment('订单id');
			$table->integer('supplier_id')->default(0)->comment('供应商id');
			$table->integer('member_id')->default(0)->comment('会员id');
			$table->boolean('apply_status')->default(0)->comment('提现状态   0 = 未提现      1 = 提交提现申请  2 = 通过申请打款成功 -1 = 驳回申请');
			$table->decimal('supplier_profit', 14)->default(0.00)->comment('供应商利润');
			$table->string('order_goods_information')->default('0')->comment('订单商品信息');
			$table->integer('created_at')->default(0);
			$table->integer('updated_at')->default(0);
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
		Schema::drop('ims_yz_supplier_order');
	}

}
