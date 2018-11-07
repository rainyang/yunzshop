<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeBecomeGoodsIdToYzMemberRelation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_member_relation')) {
            Schema::table('yz_member_relation', function (Blueprint $table) {
                if (Schema::hasColumn('yz_member_relation', 'become_goods_id')) {
                    $table->string('become_goods_id')->change();
                }
                if (!Schema::hasColumn('yz_member_relation', 'become_goods')) {
                    $table->text('become_goods')->nullable();
                }
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
        //
    }
}
