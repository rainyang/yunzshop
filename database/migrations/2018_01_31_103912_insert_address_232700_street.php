<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertAddress232700Street extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //添加新地址
        $ret = \app\common\models\Address::select()->where('areaname', '松岭区')->where('parentid', 232700)->whereLevel(3)->first();
        if (!$ret) {
            $ret_id = \app\common\models\Address::insertGetId([
                'areaname' => '松岭区',
                'parentid' => 232700,
                'level'    => 3
            ]);
        }
        $parentid = $ret ? $ret->id : $ret_id;

        $arr = ['小扬气镇','劲松镇','古源镇'];

        foreach ($arr as $key => $value) {
            $street[] = ['areaname'=> $value, 'parentid'=> $parentid, 'level'=> 4];
        }

        //2
        $ret_2 = \app\common\models\Address::select()->where('areaname', '新林区')->where('parentid', 232700)->whereLevel(3)->first();

        if (!$ret_2) {
            $id_2 = \app\common\models\Address::insertGetId([
                'areaname' => '新林区',
                'parentid' => 232700,
                'level'    => 3
            ]);
        }
        $parentid_2 = $ret_2 ? $ret_2->id : $id_2;

        $arr_2 = ['新林镇','翠岗镇','塔源镇','大乌苏镇','塔尔根镇','碧洲镇','宏图镇'];

        foreach ($arr_2 as $key => $value) {
            $street[] = ['areaname'=> $value, 'parentid'=> $parentid_2, 'level'=> 4];
        }

        //3
        $ret_3 = \app\common\models\Address::select()->where('areaname', '呼中区')->where('parentid', 232700)->whereLevel(3)->first();

        if (!$ret_3) {
            $id_3 = \app\common\models\Address::insertGetId([
                'areaname' => '呼中区',
                'parentid' => 232700,
                'level'    => 3
            ]);
        }
        $parentid_3 = $ret_3 ? $ret_3->id : $id_3;

        $arr_3 = ['呼中镇','呼源镇','碧水镇','宏伟镇'];

        foreach ($arr_3 as $key => $value) {
            $street[] = ['areaname'=> $value, 'parentid'=> $parentid_3, 'level'=> 4];
        }


        //4
        $ret_4 = \app\common\models\Address::select()->where('areaname', '加格达奇区')->where('parentid', 232700)->whereLevel(3)->first();

        if (!$ret_4) {
            $id_4 = \app\common\models\Address::insertGetId([
                'areaname' => '加格达奇区',
                'parentid' => 232700,
                'level'    => 3
            ]);
        }
        $parentid_4 = $ret_4 ? $ret_4->id : $id_4;

        $arr_4 = ['东山社区','卫东社区','红旗社区','长虹社区','曙光社区','光明社区','加北乡','白桦乡'];

        foreach ($arr_4 as $key => $value) {
            $street[] = ['areaname'=> $value, 'parentid'=> $parentid_4, 'level'=> 4];
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
