<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertAddress210184Street extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //添加新地址
        $ret = \app\common\models\Address::select()->where('areaname', '沈北新区')->where('parentid', 210100)->whereLevel(3)->first();
        if (!$ret) {
            $ret_id = \app\common\models\Address::insertGetId([
                'areaname' => '沈北新区',
                'parentid' => 210100,
                'level'    => 3
            ]);
        }
        
        $parentid = $ret ? $ret->id : $ret_id;

        \app\common\models\Street::insert([
            ['areaname'=> '新城子街道', 'parentid'=> $parentid, 'level'=> 4],
            ['areaname'=> '清水台街道', 'parentid'=> $parentid, 'level'=> 4],
            ['areaname'=> '辉山街道', 'parentid'=> $parentid, 'level'=> 4],
            ['areaname'=> '道义街道', 'parentid'=> $parentid, 'level'=> 4],
            ['areaname'=> '虎石台街道', 'parentid'=> $parentid, 'level'=> 4],
            ['areaname'=> '财落街道', 'parentid'=> $parentid, 'level'=> 4],
            ['areaname'=> '沈北街道', 'parentid'=> $parentid, 'level'=> 4],
            ['areaname'=> '马刚街道', 'parentid'=> $parentid, 'level'=> 4],
            ['areaname'=> '石佛寺街道', 'parentid'=> $parentid, 'level'=> 4],
            ['areaname'=> '黄家街道', 'parentid'=> $parentid, 'level'=> 4],
            ['areaname'=> '尹家街道', 'parentid'=> $parentid, 'level'=> 4],
            ['areaname'=> '兴隆台街道', 'parentid'=> $parentid, 'level'=> 4],
            ['areaname'=> '清泉街道', 'parentid'=> $parentid, 'level'=> 4],

        ]);
        //新城子街道、清水台街道、辉山街道、道义街道、虎石台街道、财落街道、沈北街道、马刚街道、石佛寺街道、黄家街道、尹家街道、兴隆台街道、清泉街道

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
