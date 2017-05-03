<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDateYzMemberTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('yz_member', function (Blueprint $table) {
            if (!Schema::hasColumn('yz_member', 'created_at')) {
                $table->integer('created_at')->default(0)->comment('创建时间');
            }

            if (!Schema::hasColumn('yz_member', 'updated_at')) {
                $table->integer('updated_at')->default(0)->comment('更新时间');
            }

            if (!Schema::hasColumn('yz_member', 'deleted_at')) {
                $table->integer('deleted_at')->nullable()->comment('删除时间');
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
        Schema::table('yz_member', function (Blueprint $table) {
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
            $table->dropColumn('deleted_at');
        });
    }
}
