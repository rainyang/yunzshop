<?php

use app\common\models\UniAccount;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RestartQueueLevel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $supervisor = app('supervisor');
        $supervisor->setTimeout(5000);  // microseconds
        $supervisor->restart();
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
