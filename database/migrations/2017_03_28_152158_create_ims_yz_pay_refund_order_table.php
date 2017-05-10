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
        if (!Schema::hasTable('yz_pay_refund_order')) {
            Schema::create('yz_pay_refund_order', function (Blueprint $table) {
                $table->integer('id')->primary();
                $table->integer('pay_order_id');
                $table->integer('uniacid');
                $table->integer('member_id');
                $table->string('int_order_no', 20);
                $table->string('out_order_no', 20);
                $table->integer('price');
                $table->boolean('type');
                $table->boolean('status');
                $table->integer('created_at')->default(0);
                $table->integer('updated_at')->default(0);
                $table->integer('deleted_at')->nullable();
            });
        }
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('yz_pay_refund_order');
	}

}
