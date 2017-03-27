<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzCommissionOrderTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_commission_order', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->nullable();
			$table->string('type', 60)->nullable()->comment('佣金类型');
			$table->integer('type_id')->nullable()->comment('类型ID');
			$table->integer('buy_id')->nullable()->comment('购买商品人ID');
			$table->integer('member_id')->default(0)->comment('获得佣金人');
			$table->decimal('commission_amount', 14)->nullable()->default(0.00)->comment('分销金额');
			$table->string('formula', 60)->nullable()->comment('分销金额计算公式');
			$table->integer('hierarchy')->nullable()->default(1)->comment('分销层级');
			$table->integer('commission_rate')->nullable()->default(0)->comment('佣金比例');
			$table->decimal('commission', 14)->nullable()->default(0.00)->comment('佣金');
			$table->boolean('status')->nullable()->default(0)->comment('0=>预计佣金,1=>未结算,2=>已结算,3=>未提现,4=>已提现');
			$table->integer('recrive_at')->nullable()->comment('收货时间');
			$table->integer('settle_days')->nullable()->default(0)->comment('结算天数');
			$table->integer('statement_at')->nullable()->comment('结算时间');
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
		Schema::drop('ims_yz_commission_order');
	}

}
