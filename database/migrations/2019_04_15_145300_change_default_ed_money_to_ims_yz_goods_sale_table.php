<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDefaultEdMoneyToImsYzGoodsSaleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (\Illuminate\Support\Facades\Schema::hasTable('yz_goods_sale')) {
            \Illuminate\Support\Facades\Schema::table('yz_goods_sale',
                function (Blueprint $table) {
                    if (\Illuminate\Support\Facades\Schema::hasColumn('yz_goods_sale', 'ed_money')) {
                        $table->string('ed_money',10)->nullable()->change();
                        $table->string('ed_num',10)->nullable()->change();
                        $table->string('max_once_point',10)->nullable()->change();
                    }
                });
            \app\frontend\models\goods\Sale::where('ed_money',0)->update(['ed_money'=>'']);
            \app\frontend\models\goods\Sale::where('ed_num',0)->update(['ed_money'=>'']);
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
