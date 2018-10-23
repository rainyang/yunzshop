<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertAddress440320440321440322440323Street extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $arr = (new \app\common\services\address\StreetAddress())->getStreetV2();

        foreach ($arr as $val) {

            $ret = \app\common\models\Address::select()->where('areaname', $val['areaname'])->where('parentid', $val['parentid'])->whereLevel(3)->first();
            if (!$ret) {
                $ret_id = \app\common\models\Address::insertGetId([
                    'areaname' => $val['areaname'],
                    'parentid' => $val['parentid'],
                    'level'    => 3
                ]);
            }
            $parentid = $ret ? $ret->id : $ret_id;

            foreach ($val['street'] as $key => $value) {
                \app\common\services\address\StreetAddress::verification(['areaname'=> $value, 'parentid'=> $parentid, 'level'=> 4]);
            }

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
