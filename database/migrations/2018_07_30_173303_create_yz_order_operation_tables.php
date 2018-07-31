<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYzOrderOperationTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_member_type')) {
            Schema::create('yz_member_type', function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('name');
                $table->string('code');
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
                $table->integer('deleted_at')->nullable();
            });
        }
        if (!Schema::hasTable('yz_order_operation')) {
            Schema::create('yz_order_operation', function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('name');
                $table->string('code');
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
                $table->integer('deleted_at')->nullable();
            });
        }
        if (!Schema::hasTable('yz_user_type')) {
            Schema::create('yz_user_type', function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('name');
                $table->string('code');
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
                $table->integer('deleted_at')->nullable();
            });
        }
        if (!Schema::hasTable('yz_order_status')) {
            Schema::create('yz_order_status', function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('name');
                $table->string('code');
                $table->string('sort');
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
                $table->integer('deleted_at')->nullable();
            });
        }
        if (!Schema::hasTable('yz_order_user_operation')) {
            Schema::create('yz_order_user_operation', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('user_type_id');
                $table->integer('order_operation_id');
                $table->integer('order_status_id');
                $table->string('code');
                $table->string('name');
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
                $table->integer('deleted_at')->nullable();
            });
        }
        if (!Schema::hasTable('yz_order_member_operation')) {
            Schema::create('yz_order_member_operation', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('member_type_id');
                $table->integer('order_operation_id');
                $table->integer('order_status_id');
                $table->string('code');
                $table->string('name');
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
                $table->integer('deleted_at')->nullable();
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
        if (Schema::hasTable('yz_member_type')) {
            Schema::dropIfExists('yz_member_type');
        }
        if (Schema::hasTable('yz_order_operation')) {
            Schema::dropIfExists('yz_order_operation');
        }
        if (Schema::hasTable('yz_user_type')) {
            Schema::dropIfExists('yz_user_type');
        }
        if (Schema::hasTable('yz_order_status')) {
            Schema::dropIfExists('yz_order_status');
        }
        if (Schema::hasTable('yz_order_user_operation')) {
            Schema::dropIfExists('yz_order_user_operation');
        }
        if (Schema::hasTable('yz_order_member_operation')) {
            Schema::dropIfExists('yz_order_member_operation');
        }
    }
}
