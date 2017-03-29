<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzPayRefundOrderTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_pay_refund_order', function(Blueprint $table)
		{
			$table->integer('id')->primary()->comment('编号');
			$table->integer('pay_order_id')->comment('支付单ID');
			$table->integer('uniacid')->comment('统一公众号');
			$table->integer('member_id')->comment('用户ID');
			$table->string('int_order_no', 20);
			$table->string('out_order_no', 20)->comment('退款订单号');
			$table->integer('price')->comment('退款金额');
			$table->boolean('type')->comment('退款方式 1-微信；2-支付宝');
			$table->boolean('status')->comment('退款状态  0申请 1 通过 2 驳回');
			$table->integer('created_at')->default(0)->comment('创建时间');
			$table->integer('updated_at')->default(0)->comment('更新时间');
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
		Schema::drop('ims_yz_pay_refund_order');
	}

}
