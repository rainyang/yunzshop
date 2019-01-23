<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOrderInvoice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //添加字段
        if (Schema::hasTable('yz_order')) {
            if (!Schema::hasColumn('yz_order', 'invoice')) {
                Schema::table('yz_order', function (Blueprint $table) {

                    $table->integer('invoice_type');
                    $table->integer('rise_type');
                    $table->string('call')->default(0);
                    $table->integer('company_number')->default(0);
                    $table->string('invoice')->default(0);
                });
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
