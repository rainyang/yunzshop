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
        if (!Schema::hasTable('yz_flow')) {
            Schema::create('yz_flow', function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('name');
                $table->string('code');
                $table->integer('plugin_id')->default(0);;
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
                $table->integer('deleted_at')->nullable();
            });
        }
        if (!Schema::hasTable('yz_state')) {
            Schema::create('yz_state', function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('name');
                $table->string('code')->nullable();
                $table->integer('plugin_id')->default(0);;
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
                $table->integer('deleted_at')->nullable();
            });
        }
        if (!Schema::hasTable('yz_flow_state')) {
            Schema::create('yz_flow_state', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('state_id');
                $table->integer('order');
                $table->integer('flow_id');
                $table->integer('plugin_id')->default(0);;

                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
                $table->integer('deleted_at')->nullable();
                $table->foreign('state_id')
                    ->references('id')
                    ->on('yz_state')
                    ->onDelete('cascade');
                $table->foreign('flow_id')
                    ->references('id')
                    ->on('yz_flow')
                    ->onDelete('cascade');
            });
        }
        if (!Schema::hasTable('yz_status')) {
            Schema::create('yz_status', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('model_id');
                $table->string('model_type');
                $table->string('code');
                $table->integer('state_id');

                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
                $table->integer('deleted_at')->nullable();
                $table->foreign('state_id')
                    ->references('id')
                    ->on('yz_state')
                    ->onDelete('cascade');
            });
        }
        if (!Schema::hasTable('yz_process')) {
            Schema::create('yz_process', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('model_id');
                $table->string('model_type');
                $table->integer('flow_id');
                $table->enum('state', ['processing', 'completed', 'canceled']);

                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
                $table->integer('deleted_at')->nullable();

                $table->foreign('flow_id')
                    ->references('id')
                    ->on('yz_flow')
                    ->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('yz_remittance_record')) {
            Schema::create('yz_remittance_record', function(Blueprint $table) {
                $table->integer('id', true);
                $table->integer('process_id');
                $table->text('report_url');
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
                $table->integer('deleted_at')->nullable();
                $table->foreign('process_id')
                    ->references('id')
                    ->on('yz_process')
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
        if (Schema::hasTable('yz_transfer_record')) {
            Schema::dropIfExists('yz_transfer_record');
        }
        if (Schema::hasTable('yz_process')) {
            Schema::dropIfExists('yz_process');
        }
        if (Schema::hasTable('yz_status')) {
            Schema::dropIfExists('yz_status');
        }
        if (Schema::hasTable('yz_flow_state')) {
            Schema::dropIfExists('yz_flow_state');

        }
        if (Schema::hasTable('yz_state')) {
            Schema::dropIfExists('yz_state');

        }
        if (Schema::hasTable('yz_flow')) {
            Schema::dropIfExists('yz_flow');

        }


    }
}
