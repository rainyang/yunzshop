<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MinAppTemplateMessage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_mini_app_template_message')) {
            Schema::create('yz_mini_app_template_message', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('uniacid');
                $table->string('title');
                $table->string('title_id');
                $table->string('template_id', 45);
                $table->string('formId')->nullable();
                $table->string('formId_create_time')->nullable();
                $table->integer('offset')->nullable();
                $table->string('keyword_id')->default('');
                $table->text('data', 65535)->nullable();
                $table->tinyInteger('is_default')->nullable();
                $table->tinyInteger('is_open')->default(0);
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('yz_mini_app_template_message');
    }
}
