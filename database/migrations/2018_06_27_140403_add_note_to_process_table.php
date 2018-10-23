<?php

use Illuminate\Support\Facades\Schema;
use \app\common\models\Flow;
use Illuminate\Database\Migrations\Migration;

class AddNoteToProcessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_process')) {
            if (!Schema::hasColumn('yz_process', 'note')) {
                Schema::table('yz_process', function (Blueprint $table) {
                    $table->text('note')->nullable();
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
