<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertAddress320500Street extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //添加新地址
        $ret = \app\common\models\Address::select()->where('areaname', '姑苏区')->where('parentid', 320500)->whereLevel(3)->first();
        if (!$ret) {
            $ret_id = \app\common\models\Address::insertGetId([
                'areaname' => '姑苏区',
                'parentid' => 320500,
                'level'    => 3
            ]);
        }
        $parentid = $ret ? $ret->id : $ret_id;

        $arr = ['双塔街道','沧浪街道','胥江街道','吴门桥街道','葑门街道','友新街道','观前街道','平江街道','苏锦街道','娄门街道','城北街道','桃花坞街道','石路街道','金阊街道','留园街道','虎丘街道','白洋湾街道','娄葑街道','斜塘街道','唯亭街道','胜浦街道','苏州工业园区直属镇'];

        foreach ($arr as $key => $value) {
            $street[] = ['areaname'=> $value, 'parentid'=> $parentid, 'level'=> 4];
        }

        //苏州市
        $ret_2 = \app\common\models\Address::select()->where('areaname', '吴江区')->where('parentid', 320500)->whereLevel(3)->first();
        if (!$ret_2) {
            $ret_id_2 = \app\common\models\Address::insertGetId([
                'areaname' => '吴江区',
                'parentid' => 320500,
                'level'    => 3
            ]);
        }
        $parentid_2 = $ret_2 ? $ret_2->id : $ret_id_2;

        $arr2 = ['太湖新城镇','松陵镇','同里镇','平望镇','盛泽镇','七都镇','震泽镇','桃源镇','黎里镇'];

        foreach ($arr2 as $key => $value) {
            $street[] = ['areaname'=> $value, 'parentid'=> $parentid_2, 'level'=> 4];
        }

        //杨州市
        $ret_3 = \app\common\models\Address::select()->where('areaname', '江都区')->where('parentid', 321000)->whereLevel(3)->first();
        if (!$ret_3) {
            $ret_id_3 = \app\common\models\Address::insertGetId([
                'areaname' => '江都区',
                'parentid' => 321000,
                'level'    => 3
            ]);
        }
        $parentid_3 = $ret_3 ? $ret_3->id : $ret_id_3;

        $arr3 = ['仙女镇','小纪镇','武坚镇','樊川镇','真武镇','宜陵镇','丁沟镇','郭村镇','邵伯镇','丁伙镇','大桥镇','吴桥镇','浦头镇','立新农场'];

        foreach ($arr3 as $key => $value) {
            $street[] = ['areaname'=> $value, 'parentid'=> $parentid_3, 'level'=> 4];
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
