<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzGoodsPrivilegeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_goods_privilege', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('goods_id')->index('idx_goodid')->comment('商品id');
			$table->text('show_levels', 65535)->nullable()->comment('会员等级浏览权限');
			$table->text('show_groups', 65535)->nullable()->comment('会员组浏览权限');
			$table->text('buy_levels', 65535)->nullable()->comment('会员等级购买权限');
			$table->text('buy_groups', 65535)->nullable()->comment('会员组购买权限');
			$table->integer('once_buy_limit')->nullable()->default(0)->comment('每次限购数量');
			$table->integer('total_buy_limit')->nullable()->default(0)->comment('总共限购数量');
			$table->integer('time_begin_limit')->nullable()->comment('限购开始时间');
			$table->integer('time_end_limit')->nullable()->comment('限购结束时间');
			$table->boolean('enable_time_limit')->comment('限购开关（1：开启限购；0：关闭限购）');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ims_yz_goods_privilege');
	}

}
