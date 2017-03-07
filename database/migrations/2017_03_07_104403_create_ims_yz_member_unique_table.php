<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzMemberUniqueTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yz_member_unique', function(Blueprint $table)
		{
			$table->integer('unique_id')->primary();
			$table->integer('uniacid')->nullable()->index('idx_uniacid')->comment('统一公众号');
			$table->string('unionid', 64)->index('idx_unionid')->comment('统一微信开放平台unionid');
			$table->integer('member_id')->index('idx_member_id')->comment('统一用户ID');
			$table->boolean('type')->comment('终端类型 0-公众号；1-小程序；2-微信app；3-扫码');
			$table->integer('created_at')->unsigned()->default(0)->comment('创建时间');
			$table->integer('udpated_at')->unsigned()->default(0);
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
		Schema::drop('ims_yz_member_unique');
	}

}
