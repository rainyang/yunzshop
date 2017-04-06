<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzBalanceTransferTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_balance_transfer', function(Blueprint $table)
		{
			$table->integer('id', true)->comment('主键');
			$table->integer('uniacid')->nullable()->comment('公众号ID');
			$table->integer('transferor')->nullable()->comment('转让者');
			$table->integer('recipient')->nullable()->comment('被转让者');
			$table->decimal('money', 14)->nullable()->comment('转让金额');
			$table->integer('created_at')->nullable()->comment('创建时间');
			$table->boolean('status')->nullable()->comment('-1失败，1成功');
			$table->integer('updated_at');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ims_yz_balance_transfer');
	}

}
