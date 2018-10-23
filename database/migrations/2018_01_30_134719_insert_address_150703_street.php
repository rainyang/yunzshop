<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertAddress150703Street extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //添加新地址
        $ret = \app\common\models\Address::select()->where('areaname', '扎赉诺尔区')->where('parentid', 150700)->whereLevel(3)->first();
        if (!$ret) {
            $ret_id = \app\common\models\Address::insertGetId([
                'areaname' => '扎赉诺尔区',
                'parentid' => 150700,
                'level'    => 3
            ]);
        }
        $parentid = $ret ? $ret->id : $ret_id;

        \app\common\models\Street::insert([
            ['areaname'=> '第一街道', 'parentid'=> $parentid, 'level'=> 4],
            ['areaname'=> '第二街道', 'parentid'=> $parentid, 'level'=> 4],
            ['areaname'=> '第三街道', 'parentid'=> $parentid, 'level'=> 4],
            ['areaname'=> '第四街道', 'parentid'=> $parentid, 'level'=> 4],
            ['areaname'=> '第五街道', 'parentid'=> $parentid, 'level'=> 4],
            ['areaname'=> '灵泉街道', 'parentid'=> $parentid, 'level'=> 4],
        ]);
        //第三街道、第一街道、第二街道、第四街道、第五街道、灵泉街道
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
