<?php

use Illuminate\Database\Migrations\Migration;

class CreateCronjobTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (!Schema::hasTable('cron_job')) {
            Schema::create('cron_job', function ($table) {
                $table->increments('id');
                $table->string('name');
                $table->text('return');
                $table->float('runtime');
                $table->integer('cron_manager_id')->unsigned();
                $table->index(array('name', 'cron_manager_id'));
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('cron_job');
    }

}
