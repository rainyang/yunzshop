<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertAddress340000Street extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //合肥市 巢湖市
        $ret = \app\common\models\Address::select()->where('areaname', '巢湖市')->where('parentid',  340100)->whereLevel(3)->first();
        if (!$ret) {
            $ret_id = \app\common\models\Address::insertGetId([
                'areaname' => '巢湖市',
                'parentid' =>  340100,
                'level'    => 3
            ]);
        }
        $parentid = $ret ? $ret->id : $ret_id;

        $arr = ['中庙街道','亚父街道','卧牛山街道','凤凰山街道','天河街道','半汤街道','栏杆集镇','苏湾镇','柘皋镇','银屏镇','夏阁镇','中垾镇','散兵镇','烔炀镇','黄麓镇','槐林镇','坝镇镇','庙岗乡'];

        foreach ($arr as $key => $value) {
            $street[] = ['areaname'=> $value, 'parentid'=> $parentid, 'level'=> 4];
        }


        //合肥市 庐阳区
        $ret_340103 = \app\common\models\Address::select()->where('areaname', '庐阳区')->where('parentid',  340100)->whereLevel(3)->first();
        if (!$ret_340103) {
            $ret_id_340103 = \app\common\models\Address::insertGetId([
                'areaname' => '庐阳区',
                'parentid' =>  340100,
                'level'    => 3
            ]);
        }
        $parentid_340103 = $ret_340103 ? $ret_340103->id : $ret_id_340103;

        $arr_340103 = ['庐城镇','冶父山镇','汤池镇','金牛镇','石头镇','郭河镇','同大镇','龙桥镇','矾山镇','泥河镇','罗河镇','柯坦镇','白山镇','盛桥镇','万山镇','乐桥镇','白湖镇'];

        foreach ($arr_340103 as $key => $value) {
            $street[] = ['areaname'=> $value, 'parentid'=> $parentid_340103, 'level'=> 4];
        }


        //芜湖市 无为县
        $ret_341422 = \app\common\models\Address::select()->where('areaname', '无为县')->where('parentid',  340200)->whereLevel(3)->first();
        if (!$ret_341422) {
            $ret_id_341422 = \app\common\models\Address::insertGetId([
                'areaname' => '无为县',
                'parentid' =>  340200,
                'level'    => 3
            ]);
        }
        $parentid_341422 = $ret_341422 ? $ret_341422->id : $ret_id_341422;

        $arr_341422 = ['无城镇','襄安镇','陡沟镇','石涧镇','严桥镇','开城镇','蜀山镇','牛埠镇','刘渡镇','姚沟镇','泥汊镇','福渡镇','泉塘镇','赫店镇','红庙镇','高沟镇','鹤毛乡十里墩乡','昆山乡','洪巷乡'];

        foreach ($arr_341422 as $key => $value) {
            $street[] = ['areaname'=> $value, 'parentid'=> $parentid_341422, 'level'=> 4];
        }


        //马鞍山市 博望区
        $ret_340506 = \app\common\models\Address::select()->where('areaname', '博望区')->where('parentid',  340500)->whereLevel(3)->first();
        if (!$ret_340506) {
            $ret_id_340506 = \app\common\models\Address::insertGetId([
                'areaname' => '博望区',
                'parentid' =>  340500,
                'level'    => 3
            ]);
        }
        $parentid_340506 = $ret_340506 ? $ret_340506->id : $ret_id_340506;

        $arr_340506 = ['博望镇','丹阳镇','新市镇'];

        foreach ($arr_340506 as $key => $value) {
            $street[] = ['areaname'=> $value, 'parentid'=> $parentid_340506, 'level'=> 4];
        }


        //马鞍山市 含山县
        $ret_341423 = \app\common\models\Address::select()->where('areaname', '含山县')->where('parentid',  340500)->whereLevel(3)->first();
        if (!$ret_341423) {
            $ret_id_341423 = \app\common\models\Address::insertGetId([
                'areaname' => '含山县',
                'parentid' =>  340500,
                'level'    => 3
            ]);
        }
        $parentid_341423 = $ret_341423 ? $ret_341423->id : $ret_id_341423;

        $arr_341423 = ['环峰镇','运漕镇','铜闸镇','陶厂镇','林头镇','清溪镇','仙踪镇','昭关镇'];

        foreach ($arr_341423 as $key => $value) {
            $street[] = ['areaname'=> $value, 'parentid'=> $parentid_341423, 'level'=> 4];
        }


        //马鞍山市 和县
        $ret_341424 = \app\common\models\Address::select()->where('areaname', '和县')->where('parentid',  340500)->whereLevel(3)->first();
        if (!$ret_341424) {
            $ret_id_341424 = \app\common\models\Address::insertGetId([
                'areaname' => '和县',
                'parentid' =>  340500,
                'level'    => 3
            ]);
        }
        $parentid_341424 = $ret_341424 ? $ret_341424->id : $ret_id_341424;

        $arr_341424 = ['历阳镇','白桥镇','姥桥镇','功桥镇','西埠镇','香泉镇','乌江镇','善厚镇','石杨镇'];

        foreach ($arr_341424 as $key => $value) {
            $street[] = ['areaname'=> $value, 'parentid'=> $parentid_341424, 'level'=> 4];
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
