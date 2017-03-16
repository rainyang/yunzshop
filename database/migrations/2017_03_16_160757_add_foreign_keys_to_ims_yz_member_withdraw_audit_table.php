<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToImsYzMemberWithdrawAuditTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('yz_member_withdraw_audit', function(Blueprint $table)
		{
			$table->foreign('member_withdraw_id', 'fk_member_withdraw_audit_member_withdraw')->references('id')->on('ims_yz_member_withdraw')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('yz_member_withdraw_audit', function(Blueprint $table)
		{
			$table->dropForeign('fk_member_withdraw_audit_member_withdraw');
		});
	}

}
