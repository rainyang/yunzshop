<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertAddress511903Street extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         //添加新地址
        $ret = \app\common\models\Address::select()->where('areaname', '恩阳区')->where('parentid', 511900)->whereLevel(3)->first();
        if (!$ret) {
            $ret_id = \app\common\models\Address::insertGetId([
                'areaname' => '恩阳区',
                'parentid' => 511900,
                'level'    => 3
            ]);
        }

        $parentid = $ret ? $ret->id : $ret_id;

        $arr = ['恩阳镇','登科镇','明阳镇','玉山镇','茶坝镇','观音井镇','花丛镇','柳林镇','下八庙镇','渔溪镇','青木镇','三河场镇','三汇镇','上八庙镇','石城乡','兴隆场乡','关公乡','三星乡','舞凤乡','双胜乡','群乐乡','万安乡','尹家乡','九镇乡','玉井乡','义兴乡'];

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
