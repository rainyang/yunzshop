<?php

use Illuminate\Database\Migrations\Migration;

class InsertAddress extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $ret = \app\common\models\Address::select()->where('areaname', '新吴区')->where('parentid', 320200)->whereLevel(3)->first();
        if (!$ret) {
            $ret_id = \app\common\models\Address::insertGetId([
                'areaname' => '新吴区',
                'parentid' => 320200,
                'level'    => 3
            ]);
            \app\common\models\Street::insert([
                ['areaname' => '鸿山', 'parentid' => $ret_id, 'level' => 4],
                ['areaname' => '江溪', 'parentid' => $ret_id, 'level' => 4],
                ['areaname' => '旺庄', 'parentid' => $ret_id, 'level' => 4],
                ['areaname' => '硕放', 'parentid' => $ret_id, 'level' => 4],
                ['areaname' => '梅村', 'parentid' => $ret_id, 'level' => 4],
                ['areaname' => '新安', 'parentid' => $ret_id, 'level' => 4],
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
