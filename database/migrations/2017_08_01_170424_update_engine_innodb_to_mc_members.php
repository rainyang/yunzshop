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
        if (\Schema::hasTable('mc_members')) {
            $db_name =\YunShop::app()->config['db']['master']['database'];
            $engine = DB::select("show table status from ".$db_name."  where name='ims_mc_members'");
            if (isset($engine['0']['Engine']) && strtolower($engine['0']['Engine']) == 'myisam') {
                DB::statement("ALTER TABLE ims_mc_members engine = InnoDB");
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
