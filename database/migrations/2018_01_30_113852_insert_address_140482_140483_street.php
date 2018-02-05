<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertAddress140482140483Street extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //添加新地址
        $ret = \app\common\models\Address::select()->where('areaname', '城区')->where('parentid', 140400)->whereLevel(3)->first();
        if (!$ret) {
            $ret_id = \app\common\models\Address::insertGetId([
                'areaname' => '城区',
                'parentid' => 140400,
                'level'    => 3
            ]);
        }
        $parentid = $ret ? $ret->id : $ret_id;

        \app\common\models\Street::insert([
            ['areaname'=> '东街街道', 'parentid'=> $parentid, 'level'=> 4],
            ['areaname'=> '西街街道', 'parentid'=> $parentid, 'level'=> 4],
            ['areaname'=> '英雄南路街道', 'parentid'=> $parentid, 'level'=> 4],
            ['areaname'=> '英雄中路街道', 'parentid'=> $parentid, 'level'=> 4],
            ['areaname'=> '紫金街街道', 'parentid'=> $parentid, 'level'=> 4],
            ['areaname'=> '太行东街街道', 'parentid'=> $parentid, 'level'=> 4],
            ['areaname'=> '太行西街街道', 'parentid'=> $parentid, 'level'=> 4],
            ['areaname'=> '延安南路街道', 'parentid'=> $parentid, 'level'=> 4],
            ['areaname'=> '常青街道', 'parentid'=> $parentid, 'level'=> 4],
            ['areaname'=> '五马街道', 'parentid'=> $parentid, 'level'=> 4],
            ['areaname'=> '长治高新技术开发区', 'parentid'=> $parentid, 'level'=> 4],
        ]);
        //东街街道、西街街道、英雄南路街道、英雄中路街道、紫金街街道、太行东街街道、太行西街街道、延安南路街道、常青街道、五马街道、长治高新技术开发区
        
        $ret2 = \app\common\models\Address::select()->where('areaname', '郊区')->where('parentid', 140400)->whereLevel(3)->first();
        if (!$ret2) {
            $ret_id2 = \app\common\models\Address::insertGetId([
                'areaname' => '郊区',
                'parentid' => 140400,
                'level'    => 3
            ]);
        }
        $parentid2 = $ret2 ? $ret2->id : $ret_id2;

        \app\common\models\Street::insert([
            ['areaname'=> '长北街道', 'parentid'=> $parentid2, 'level'=> 4],
            ['areaname'=> '故县街道', 'parentid'=> $parentid2, 'level'=> 4],
            ['areaname'=> '老顶山镇', 'parentid'=> $parentid2, 'level'=> 4],
            ['areaname'=> '堠北庄镇', 'parentid'=> $parentid2, 'level'=> 4],
            ['areaname'=> '大辛庄镇', 'parentid'=> $parentid2, 'level'=> 4],
            ['areaname'=> '马厂镇', 'parentid'=> $parentid2, 'level'=> 4],
            ['areaname'=> '黄碾镇', 'parentid'=> $parentid2, 'level'=> 4],
            ['areaname'=> '西白兔乡', 'parentid'=> $parentid2, 'level'=> 4],

        ]);
        //长北街道、故县街道、老顶山镇、堠北庄镇、大辛庄镇、马厂镇、黄碾镇、西白兔乡
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
