<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertAddress220625Street extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //添加新地址
        $ret = \app\common\models\Address::select()->where('areaname', '江源区')->where('parentid', 220600)->whereLevel(3)->first();
        if (!$ret) {
            $ret_id = \app\common\models\Address::insertGetId([
                'areaname' => '江源区',
                'parentid' => 220600,
                'level'    => 3
            ]);
        }
        $parentid = $ret ? $ret->id : $ret_id;

        $arr = ['孙家堡子街道','江源街道','正岔街道','城墙街道','湾沟镇','松树镇','砟子镇','石人镇','大阳岔镇','大石人镇'];
        foreach ($arr as $key => $value) {
            $street[] = ['areaname'=> $value, 'parentid'=> $parentid, 'level'=> 4];
            
        }

        \app\common\models\Street::insert($street);

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
