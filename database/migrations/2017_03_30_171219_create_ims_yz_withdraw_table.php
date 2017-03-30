<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzWithdrawTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_withdraw', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->nullable()->comment('店铺ID');
			$table->integer('member_id')->nullable()->comment('会员ID ');
			$table->string('type', 60)->nullable()->comment('提现类型');
			$table->string('type_id', 60)->nullable()->comment('关联 收入订单ID');
			$table->decimal('amounts', 14)->nullable()->comment('提现金额');
			$table->decimal('poundage', 14)->nullable()->comment('手续费');
			$table->integer('poundage_rate')->nullable()->comment('手续费比例');
			$table->string('pay_way', 100)->nullable()->comment('打款方式');
			$table->boolean('status')->nullable()->comment('0:未审核 1：未打款 2：已打款 -1：无效');
			$table->integer('created_at')->nullable();
			$table->integer('updated_at')->nullable();
			$table->integer('deleted_at')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ims_yz_withdraw');
	}

}
