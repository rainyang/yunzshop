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

                    $table->integer('invoice_type')->nullable();
                    $table->integer('rise_type')->nullable();
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
        if (Schema::hasTable('yz_order')) {
            if (Schema::hasColumn('invoice_type', 'rise_type','company_number','call','invoice')) {
                Schema::table('yz_order', function (Blueprint $table) {
                    $table->dropColumn('invoice_type');
                    $table->dropColumn('rise_type');
                    $table->dropColumn('call');
                    $table->dropColumn('company_number');
                    $table->dropColumn('invoice');
                });
            }
        }
        //
    }
}
