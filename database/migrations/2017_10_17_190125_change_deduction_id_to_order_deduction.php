<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDeductionIdToOrderDeduction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_order_deduction')) {

            if (\Schema::hasColumn('yz_order_deduction', 'deduction_id')) {
                \Schema::table('yz_order_deduction', function ($table) {

                    $table->string('deduction_id', 50)->default('')->change();
                    $table->renameColumn('deduction_id', 'code');

                    $table->decimal('qty', 10)->default(0.00)->change();
                    $table->renameColumn('qty', 'coin');
                });
                // id改为对应code
                $orderDeductions = \app\common\models\order\OrderDeduction::get();
                $orderDeductions->each(function ($orderDeductions) {
                    if ($orderDeductions->code == 1) {
                        $orderDeductions->code = 'point';
                    } elseif ($orderDeductions->code == 2) {
                        $orderDeductions->code = 'love';
                    } elseif ($orderDeductions->code == 3) {
                        $orderDeductions->code = 'coin';
                    }
                    $orderDeductions->save();
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
