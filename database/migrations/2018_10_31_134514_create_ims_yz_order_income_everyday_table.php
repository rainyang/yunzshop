<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


class CreateImsYzOrderIncomeEverydayTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_order_income_everyday')) {
            Schema::create('yz_order_income_everyday', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('uniacid')->nullable();
                $table->decimal('amount', 14)->nullable();
                $table->decimal('undividend', 14)->nullable();
                $table->decimal('shop', 14)->nullable();
                $table->decimal('supplier', 14)->nullable();
                $table->decimal('cashier', 14)->nullable();
                $table->decimal('store', 14)->nullable();
                $table->integer('day_time');
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
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
		Schema::dropIfExists('yz_order_income_everyday');
	}

}
