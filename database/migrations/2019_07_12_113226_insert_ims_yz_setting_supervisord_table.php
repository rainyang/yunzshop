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
            $sql = "INSERT INTO `ims_yz_setting` (`id`, `uniacid`, `group`, `key`, `type`, `value`) SELECT NULL, '0', 'shop', 'supervisor', 'string', 'http://127.0.0.1' WHERE NOT EXISTS(SELECT * FROM `ims_yz_setting` WHERE `uniacid` = 0 AND `group` = 'shop' AND `key` = 'supervisor')";
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
