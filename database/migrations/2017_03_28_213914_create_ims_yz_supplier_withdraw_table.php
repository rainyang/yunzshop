<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzSupplierWithdrawTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_supplier_withdraw', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('member_id')->default(0)->comment('会员ID');
			$table->integer('supplier_id')->default(0)->comment('供应商ID');
			$table->boolean('status')->default(0)->comment('1 = 提交提现申请  2 = 审核通过 3 = 打款成功 -1 = 驳回申请');
			$table->decimal('money', 14)->default(0.00)->comment('提现金额');
			$table->string('order_ids')->default('0')->comment('当前提现记录的所有orderids');
			$table->integer('created_at')->default(0);
			$table->integer('updated_at')->default(0);
			$table->integer('deleted_at')->default(0);
			$table->integer('uniacid')->nullable()->default(0);
			$table->string('apply_sn', 50)->nullable()->default('0');
			$table->boolean('type')->nullable()->default(0)->comment('1 提现到银行卡 2 提现到微信');
			$table->integer('pay_time')->nullable()->default(0)->comment('打款时间,完成时间');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ims_yz_supplier_withdraw');
	}

}
