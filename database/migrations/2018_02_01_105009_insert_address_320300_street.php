<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertAddress320300Street extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //添加新地址
        $ret = \app\common\models\Address::select()->where('areaname', '铜山区')->where('parentid', 320300)->whereLevel(3)->first();
        if (!$ret) {
            $ret_id = \app\common\models\Address::insertGetId([
                'areaname' => '铜山区',
                'parentid' => 320300,
                'level'    => 3
            ]);
        }
        $parentid = $ret ? $ret->id : $ret_id;

        $arr = ['三河尖街道','张双楼街道','垞城街道','张集街道','义安街道','利国街道','电厂街道','拾屯街道','铜山街道','新区街道','三堡街道','何桥镇','黄集镇','马坡镇','郑集镇','柳新镇','刘集镇','大彭镇','汉王镇','棠张镇','张集镇','房村镇','伊庄镇','单集镇','利国镇','徐庄镇','大许镇','茅村镇','柳泉镇','国营沿湖农场','徐州高新技术产业开发区'];

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
