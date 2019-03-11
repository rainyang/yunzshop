<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToApp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_uniacid_app')) {
            if (!Schema::hasColumn('yz_uniacid_app', 'url')) {
                
                Schema::table('yz_uniacid_app', function (Blueprint $table) {
                    
                    $table->string('url')->nullable();

                    // $table->softDeletes();
                    // $table->timestamps();
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
