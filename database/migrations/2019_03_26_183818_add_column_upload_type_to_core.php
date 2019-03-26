<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnUploadTypeToCore extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_core_attachment')) {
           
            if (!Schema::hasColumn('yz_core_attachment', 'upload_type')) {
                Schema::table('yz_core_attachment', function (Blueprint $table) {
                    
                    $table->tinyInteger('upload_type')->nullable();
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
