<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzOrderTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('yz_order', function(Blueprint $table)
		{
            if (!Schema::hasColumn('yz_order', 'order_pay_id')) {

                $table->integer('order_pay_id')->default(0);
            }

        });
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('yz_order', function (Blueprint $table) {
            $table->dropColumn('order_pay_id');
        });
	}

}
