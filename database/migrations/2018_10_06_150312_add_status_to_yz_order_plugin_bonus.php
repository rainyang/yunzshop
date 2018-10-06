<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusToYzOrderPluginBonus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_order_plugin_bonus')) {
            Schema::table('yz_order_plugin_bonus', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_order_plugin_bonus', 'content')) {
                    $table->string('content')->nullable();
                }
                if (!Schema::hasColumn('yz_order_plugin_bonus', 'status')) {
                    $table->tinyInteger('status')->default(0);
                }
                if (!Schema::hasColumn('yz_order_plugin_bonus', 'undivided')) {
                    $table->decimal('undivided',14)->default(0.00);
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
