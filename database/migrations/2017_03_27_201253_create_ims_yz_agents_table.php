<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzAgentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_agents', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->nullable();
			$table->integer('member_id')->nullable();
			$table->integer('parent_id')->nullable()->default(0)->comment('上一级ID');
			$table->integer('agent_level_id')->nullable()->default(0)->comment('分销商等级ID');
			$table->boolean('is_black')->nullable()->default(0)->comment('0:正常分销商 1：加入黑名单');
			$table->decimal('commission_total', 14)->nullable()->default(0.00)->comment('累计佣金');
			$table->decimal('commission_pay', 14)->nullable()->comment('已打款佣金');
			$table->boolean('agent_not_upgrade')->nullable()->default(0)->comment('不自动升级 0：自动升级 1：不自动升级');
			$table->text('content', 65535)->nullable()->comment('备注');
			$table->integer('created_at')->nullable()->comment('创建时间、成为分销商时间');
			$table->integer('updated_at')->nullable()->comment('修改时间');
			$table->integer('deleted_at')->nullable()->comment('删除时间');
			$table->string('parent', 20)->nullable();
			$table->index(['uniacid','parent'], 'idx_uniacid_parent');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ims_yz_agents');
	}

}
