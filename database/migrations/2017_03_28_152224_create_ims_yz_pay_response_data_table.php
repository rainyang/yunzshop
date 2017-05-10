<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzPayResponseDataTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_pay_response_data')) {
            Schema::create('yz_pay_response_data', function (Blueprint $table) {
                $table->integer('id')->primary();
                $table->integer('uniacid');
                $table->integer('order_id');
                $table->boolean('type');
                $table->boolean('third_type');
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
		Schema::dropIfExists('yz_pay_response_data');
	}

}
