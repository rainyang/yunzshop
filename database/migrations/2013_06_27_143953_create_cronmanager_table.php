<?php

use Illuminate\Database\Migrations\Migration;

class CreateCronmanagerTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (!Schema::hasTable('cron_manager')) {
            Schema::create('cron_manager', function ($table) {
                $table->increments('id');
                $table->dateTime('rundate');
                $table->float('runtime');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('cron_manager');
    }

}