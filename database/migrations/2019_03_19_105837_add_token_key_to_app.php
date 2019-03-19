<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTokenKeyToApp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_uniacid_app')) {

            
            if (!Schema::hasColumn('yz_uniacid_app', 'token') ) {
                
                Schema::table('yz_uniacid_app', function (Blueprint $table) {
                    
                    $table->string('token')->nullable();
                });
            }
            if (!Schema::hasColumn('yz_uniacid_app', 'encodingaeskey')) {
                Schema::table('yz_uniacid_app', function (Blueprint $table) {
                    
                    $table->string('encodingaeskey')->nullable();
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
