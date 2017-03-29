<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzPayOrderTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_pay_order', function(Blueprint $table)
		{
			$table->integer('id', true)->comment('编号');
			$table->integer('uniacid')->index('idx_uniacid')->comment('统一公众号');
			$table->integer('member_id')->index('idx_member_id')->comment('用户ID');
			$table->string('int_order_no', 32)->nullable()->comment('支付单号');
			$table->string('out_order_no', 32)->default('0')->index('idx_order_no')->comment('订单号');
			$table->boolean('status')->default(0)->comment('支付状态 0-未支付；1-待支付；2-已支付');
			$table->integer('price')->comment('支付金额');
			$table->boolean('type')->comment('支付类型(1支付、2充值)');
			$table->boolean('third_type')->comment('第三方支付类型');
			$table->integer('created_at')->default(0)->comment('创建时间');
			$table->integer('updated_at')->default(0)->comment('更像时间');
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
		Schema::drop('ims_yz_pay_order');
	}

}
