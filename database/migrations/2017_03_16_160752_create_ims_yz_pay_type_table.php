<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzPayTypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_pay_type', function(Blueprint $table)
		{
			$table->integer('id', true)->comment('编号');
			$table->string('tag', 45)->comment('支付类型标签');
			$table->string('name', 45)->comment('支付类型名称');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ims_yz_pay_type');
	}

}
