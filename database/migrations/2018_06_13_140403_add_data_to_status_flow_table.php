<?php

use Illuminate\Support\Facades\Schema;
use \app\common\models\Flow;
use Illuminate\Database\Migrations\Migration;

class AddDataToStatusFlowTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_status')) {
            return;
        }
        /**
         * @var Flow $flow
         */
        $flow = \app\common\models\Flow::create([
            'name' => '汇款支付',
            'code' => \app\frontend\modules\payType\Remittance::class,
        ]);
        $flow->states()->saveMany([
            new \app\common\models\State([
                    'name' => '待汇款',
                    'code' => 'waitRemittance',
                ]
            ),
            new \app\common\models\State([
                    'name' => '待收款',
                    'code' => 'waitReceipt',
                ]
            ),
            new \app\common\models\State([
                    'name' => '已完成',
                    'code' => 'completed',
                ]
            ),
            new \app\common\models\State([
                    'name' => '已取消',
                    'code' => 'canceled',
                ]
            ),
        ]);
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
