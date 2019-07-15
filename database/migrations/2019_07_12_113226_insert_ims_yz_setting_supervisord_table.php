<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertImsYzSettingSupervisordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_setting')) {
            $sql = "INSERT INTO `ims_yz_setting` (`id`, `uniacid`, `group`, `key`, `type`, `value`) VALUES(NULL, '0', 'shop', 'supervisor', 'string', 'http://127.0.0.1')";
            \Illuminate\Support\Facades\DB::unprepared($sql);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('yz_setting');
    }
}
