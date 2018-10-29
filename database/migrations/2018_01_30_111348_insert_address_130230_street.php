<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertAddress130230Street extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //添加新地址
        $ret = \app\common\models\Address::select()->where('areaname', '曹妃甸区')->where('parentid', 130200)->whereLevel(3)->first();
        if (!$ret) {
            $ret_id = \app\common\models\Address::insertGetId([
                'areaname' => '曹妃甸区',
                'parentid' => 130200,
                'level'    => 3
            ]);
        }

        $parentid = $ret ? $ret->id : $ret_id;

        // 唐海镇、滨海镇、柳赞镇、一农场、三农场、四农场、五农场、六农场、七农场、八农场、九农场、十农场、十一农场、八里滩养殖场、十里海养殖场、南堡经济开发区、曹妃甸工业区、唐山湾生态城
        \app\common\models\Street::insert([
            ['areaname'=> '唐海镇', 'parentid'=> $parentid, 'level'=> 4],
            ['areaname'=> '滨海镇', 'parentid'=> $parentid, 'level'=> 4],
            ['areaname'=> '柳赞镇', 'parentid'=> $parentid, 'level'=> 4],
            ['areaname'=> '一农场', 'parentid'=> $parentid, 'level'=> 4],
            ['areaname'=> '三农场', 'parentid'=> $parentid, 'level'=> 4],
            ['areaname'=> '四农场', 'parentid'=> $parentid, 'level'=> 4],
            ['areaname'=> '五农场', 'parentid'=> $parentid, 'level'=> 4],
            ['areaname'=> '六农场', 'parentid'=> $parentid, 'level'=> 4],
            ['areaname'=> '七农场', 'parentid'=> $parentid, 'level'=> 4],
            ['areaname'=> '八农场', 'parentid'=> $parentid, 'level'=> 4],
            ['areaname'=> '九农场', 'parentid'=> $parentid, 'level'=> 4],
            ['areaname'=> '十农场', 'parentid'=> $parentid, 'level'=> 4],
            ['areaname'=> '十一农场', 'parentid'=> $parentid, 'level'=> 4],
            ['areaname'=> '八里滩养殖场', 'parentid'=> $parentid, 'level'=> 4],
            ['areaname'=> '十里海养殖场', 'parentid'=> $parentid, 'level'=> 4],
            ['areaname'=> '南堡经济开发区', 'parentid'=> $parentid, 'level'=> 4],
            ['areaname'=> '曹妃甸工业区', 'parentid'=> $parentid, 'level'=> 4],
            ['areaname'=> '唐山湾生态城', 'parentid'=> $parentid, 'level'=> 4],
        ]);

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
