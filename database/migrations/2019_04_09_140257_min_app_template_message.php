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
        if (!Schema::hasTable('yz_min_app_template_message')) {
            Schema::create('yz_min_app_template_message', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('uniacid');
                $table->string('title');
                $table->string('title_id');
                $table->string('template_id', 45);
                $table->integer('offset')->nullable()->default(0);
                $table->string('keyword_id')->default('');
                $table->text('data', 65535)->nullable()->default(0);;
                $table->tinyInteger('is_default')->nullable()->default(0);
                $table->tinyInteger('is_open')->default(0);
                $table->integer('created_at')->nullable()->default(0);
                $table->integer('updated_at')->nullable()->default(0);
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
        Schema::dropIfExists('yz_min_app_template_message');
    }
}
