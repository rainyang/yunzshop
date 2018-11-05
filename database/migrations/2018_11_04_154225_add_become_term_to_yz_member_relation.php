<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBecomeTermToYzMemberRelation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_member_relation')) {
            if (!Schema::hasColumn('yz_member_relation', 'become_term')) {
                Schema::table('yz_member_relation', function (Blueprint $table) {
                    $table->string('become_term')->nullable();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
