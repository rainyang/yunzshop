<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzPayRequestDataTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_pay_request_data')) {
            Schema::create('yz_pay_request_data', function (Blueprint $table) {
                $table->integer('id')->primary();
                $table->integer('uniacid');
                $table->integer('order_id');
                $table->boolean('type');
                $table->boolean('third_type')->nullable();
                $table->text('params', 65535);
                $table->integer('created_at')->default(0);
                $table->integer('updated_at');
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
		Schema::drop('yz_pay_request_data');
	}

}
