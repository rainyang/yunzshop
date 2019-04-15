<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateEngineInnodbToMcMembers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (env('APP_Framework') == 'platform') {
            $mc_members =  'yz_mc_members';
        } else {
            $mc_members = 'mc_members';
        }
        if (\Schema::hasTable($mc_members)) {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE ".app('db')->getTablePrefix().$mc_members." engine = InnoDB");
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
