<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertAddress230100Street extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //添加新地址
        $ret1 = \app\common\models\Address::select()->where('areaname', '香坊区')->where('parentid', 230100)->whereLevel(3)->first();
        if (!$ret1) {
            $ret_id1 = \app\common\models\Address::insertGetId([
                'areaname' => '香坊区',
                'parentid' => 230100,
                'level'    => 3
            ]);
        }
        $parentid1 = $ret1 ? $ret1->id : $ret_id1;

        $arr = ['香坊大街街道','安埠街道','通天街道','新香坊街道','铁东街道','新成街道','红旗街道','六顺街道','建筑街道','哈平路街道','安乐街道','健康路街道','大庆路街道','进乡街道','通乡街道','和平路街道','民生路街道','文政街道','王兆街道','黎明街道','成高子镇','幸福镇','朝阳镇','向阳乡','香坊实验农场','香坊区农垦'];

        foreach ($arr as $key => $value) {
            $street1[] = ['areaname'=> $value, 'parentid'=> $parentid1, 'level'=> 4];
        }

        \app\common\models\Street::insert($street1);


        $ret2 = \app\common\models\Address::select()->where('areaname', '阿城区')->where('parentid', 230100)->whereLevel(3)->first();
        if (!$ret2) {
            $ret_id2 = \app\common\models\Address::insertGetId([
                'areaname' => '阿城区',
                'parentid' => 230100,
                'level'    => 3
            ]);
        }
        $parentid2 = $ret2 ? $ret2->id : $ret_id2;

        $arr2 = ['金城街道','金都街道','通城街道','河东街道','阿什河街道','玉泉街道','新利街道','双丰街道','舍利街道','小岭街道','亚沟街道','交界街道','蜚克图镇','平山镇','松峰山镇','红星镇','金龙山镇','杨树镇','料甸镇','阿城原种场'];

        foreach ($arr2 as $key => $value) {
            $street_230181[] = ['areaname'=> $value, 'parentid'=> $parentid2, 'level'=> 4];
        }
        \app\common\models\Street::insert($street_230181);
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
