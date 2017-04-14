<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzMemberIncomeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_member_income', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->comment('店铺ID');
			$table->integer('member_id')->comment('会员ID');
			$table->string('type', 60)->default('')->comment('收入类型');
			$table->integer('type_id')->nullable();
			$table->string('type_name', 120)->nullable()->comment('类型名称');
			$table->decimal('amount', 14)->default(0.00)->comment('收入金额');
			$table->boolean('status')->default(0)->comment('状态；0未提现，1已提现');
			$table->text('detail', 65535)->nullable()->comment('收入明细（json）');
			$table->string('create_month', 20)->nullable()->default('');
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
		Schema::drop('ims_yz_member_income');
	}

}
