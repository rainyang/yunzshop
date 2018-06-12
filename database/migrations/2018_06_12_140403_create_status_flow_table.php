<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStatusFlowTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_status_flow')) {
            Schema::create('yz_status_flow', function(Blueprint $table) {
                $table->integer('id', true);
                $table->string('name',255);
                $table->string('code',100);
                $table->integer('plugin_id');
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
                $table->integer('deleted_at')->nullable();
            });
        }
        if (!Schema::hasTable('yz_status')) {
            Schema::create('yz_status', function(Blueprint $table) {
                $table->integer('id', true);
                $table->string('name',255);
                $table->string('code',100);
                $table->integer('plugin_id');
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
                $table->integer('deleted_at')->nullable();
            });
        }
        if (!Schema::hasTable('yz_status_flow_status')) {
            Schema::create('yz_status_flow_status', function(Blueprint $table) {
                $table->integer('id', true);
                $table->integer('status_id');
                $table->integer('status_flow_id');
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
                $table->integer('deleted_at')->nullable();
                $table->foreign('status_id')
                    ->references('id')
                    ->on('yz_status')
                    ->onDelete('cascade');
                $table->foreign('status_flow_id')
                    ->references('id')
                    ->on('yz_status_flow')
                    ->onDelete('cascade');
            });
        }
        if (!Schema::hasTable('yz_order_status')) {
            Schema::create('yz_status_flow_status', function(Blueprint $table) {
                $table->integer('id', true);
                $table->integer('order_id');
                $table->integer('status_id');
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
                $table->integer('deleted_at')->nullable();
                $table->foreign('order_id')
                    ->references('id')
                    ->on('yz_order')
                    ->onDelete('cascade');
                $table->foreign('status_id')
                    ->references('id')
                    ->on('yz_status')
                    ->onDelete('cascade');
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
        if (Schema::hasTable('yz_status_flow_status')) {
            Schema::dropIfExists('yz_status_flow_status');

        }
        if (Schema::hasTable('yz_status_flow')) {
            Schema::dropIfExists('yz_status_flow');

        }
        if (Schema::hasTable('yz_status')) {
            Schema::dropIfExists('yz_status');

        }
    }
}
