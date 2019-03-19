<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddKeyToApp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         if (Schema::hasTable('yz_uniacid_app')) {

            
            if (!Schema::hasColumn('yz_uniacid_app', 'key') ) {
                
                Schema::table('yz_uniacid_app', function (Blueprint $table) {
                    
                    $table->string('key')->nullable();
                });
            }
            if (!Schema::hasColumn('yz_uniacid_app', 'secret')) {
                Schema::table('yz_uniacid_app', function (Blueprint $table) {
                    
                    $table->string('secret')->nullable();
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
