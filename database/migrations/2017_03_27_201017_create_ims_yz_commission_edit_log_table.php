<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzCommissionEditLogTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_commission_edit_log', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('role', 100)->nullable()->comment('操作人');
			$table->text('content', 65535)->nullable()->comment('操作内容');
			$table->string('type', 60)->nullable()->comment('操作内容');
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
		Schema::drop('ims_yz_commission_edit_log');
	}

}
