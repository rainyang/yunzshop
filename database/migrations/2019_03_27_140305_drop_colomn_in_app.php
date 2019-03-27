<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropColomnInApp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_uniacid_app')) {
           
            if (Schema::hasColumn('yz_uniacid_app', 'uniacid')) {
                
                Schema::table('yz_uniacid_app', function (Blueprint $table) {
                    
                    $table->dropColumn('uniacid');
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
        
    }
}
