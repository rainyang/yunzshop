<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;


class UpdateAddressHongKong extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $ret_id = \app\common\models\Address::insertGetId([
                        'areaname' => '香港特别行政区',
                        'parentid' => 810000,
                        'level'    => 2
                    ]);
        $bool = DB::table('yz_address')->where('parentid', 810000)->whereNotIn('id', [$ret_id])->update(['parentid'=> $ret_id ,'level'=> 3]);

       if ($bool) {
            \app\common\models\Street::insert([
                //香港岛：中西区、湾仔区、东区、南区
                ['areaname'=> '中西区', 'parentid'=> 810100, 'level'=> 4],
                ['areaname'=> '湾仔区', 'parentid'=> 810100, 'level'=> 4],
                ['areaname'=> '东区', 'parentid'=> 810100, 'level'=> 4],
                ['areaname'=> '南区', 'parentid'=> 810100, 'level'=> 4],
                //九龙半岛：油尖旺区、深水埗区、九龙城区、黄大仙区、观塘区
                ['areaname'=> '油尖旺区', 'parentid'=> 810200, 'level'=> 4],
                ['areaname'=> '深水埗区', 'parentid'=> 810200, 'level'=> 4],
                ['areaname'=> '九龙城区', 'parentid'=> 810200, 'level'=> 4],
                ['areaname'=> '黄大仙区', 'parentid'=> 810200, 'level'=> 4],
                ['areaname'=> '观塘区', 'parentid'=> 810200, 'level'=> 4],
                //新界：北区、大埔区、沙田区、西贡区、荃湾区、屯门区、元朗区、葵青区、离岛区
                ['areaname'=> '北区', 'parentid'=> 810300, 'level'=> 4],
                ['areaname'=> '大埔区', 'parentid'=> 810300, 'level'=> 4],
                ['areaname'=> '沙田区', 'parentid'=> 810300, 'level'=> 4],
                ['areaname'=> '西贡区', 'parentid'=> 810300, 'level'=> 4],
                ['areaname'=> '荃湾区', 'parentid'=> 810300, 'level'=> 4],
                ['areaname'=> '屯门区', 'parentid'=> 810300, 'level'=> 4],
                ['areaname'=> '元朗区', 'parentid'=> 810300, 'level'=> 4],
                ['areaname'=> '葵青区', 'parentid'=> 810300, 'level'=> 4],
                ['areaname'=> '离岛区', 'parentid'=> 810300, 'level'=> 4],
                
            ]);
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
