<?php

use Illuminate\Support\Facades\Schema;
use \app\common\models\Flow;
use Illuminate\Database\Migrations\Migration;

class AddRemittanceAuditToStatusFlowTable extends Migration
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
        $this->audit();
    }

    private function audit()
    {
        /**
         * @var Flow $flow
         */
        $flow = \app\common\models\Flow::create([
            'name' => '转账审核',
            'code' => 'remittanceAudit',
        ]);
        $flow->setManyStatus([
                \app\common\models\Status::where('code', 'waitAudit')->value('id') => ['order' => 0],
                \app\common\models\Status::where('code', 'passed')->value('id') => ['order' => 0],
                \app\common\models\Status::where('code', 'canceled')->value('id') => ['order' => -1],
                \app\common\models\Status::where('code', 'refused')->value('id') => ['order' => -2],
            ]
        );
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
