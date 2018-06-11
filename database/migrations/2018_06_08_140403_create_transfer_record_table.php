<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransferRecordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_transfer_record')) {
            Schema::create('yz_transfer_record', function(Blueprint $table) {
                $table->integer('id', true);
                $table->integer('order_pay_id')->index('idx_order_pay_id');
                $table->text('report_url');
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
        if (Schema::hasTable('yz_order_pay_order')) {
            Schema::dropIfExists('yz_order_pay_order');

        }
    }
}
