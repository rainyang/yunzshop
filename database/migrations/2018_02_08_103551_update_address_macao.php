<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateAddressMacao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $ret_id = \app\common\models\Address::insertGetId([
                        'areaname' => '澳门特别行政区',
                        'parentid' => 820000,
                        'level'    => 2
                    ]);
        $bool = DB::table('yz_address')->where('parentid', 820000)->whereNotIn('id', [$ret_id])->update(['parentid'=> $ret_id ,'level'=> 3]);

       if ($bool) {
            \app\common\models\Street::insert([
                //澳门半岛：花地玛堂区、圣安多尼堂区、大堂区、望德堂区、风顺堂区
                ['areaname'=> '花地玛堂区', 'parentid'=> 820100, 'level'=> 4],
                ['areaname'=> '圣安多尼堂区', 'parentid'=> 820100, 'level'=> 4],
                ['areaname'=> '大堂区', 'parentid'=> 820100, 'level'=> 4],
                ['areaname'=> '望德堂区', 'parentid'=> 820100, 'level'=> 4],
                ['areaname'=> '风顺堂区', 'parentid'=> 820100, 'level'=> 4],
                //离岛：嘉模堂区、圣方济各堂区
                ['areaname'=> '嘉模堂区', 'parentid'=> 820200, 'level'=> 4],
                ['areaname'=> '圣方济各堂区', 'parentid'=> 820200, 'level'=> 4],
               
            ]);
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
