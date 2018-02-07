<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAddressUrbanDistricictStreet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $arr = \app\common\services\address\UrbanDistricictAddres::$completion;
        

        foreach ($arr as $val) {

            $ret = \app\common\models\Address::select('areaname', 'parentid', 'level')->where('parentid', $val['parentid'])->whereLevel(3)->get();

            \app\common\models\Address::where('parentid', $val['parentid'])->whereLevel(3)->delete();
            
            $ret_id = \app\common\models\Address::insertGetId([
                        'areaname' => $val['areaname'],
                        'parentid' => $val['parentid'],
                        'level'    => 3
                    ]);
            foreach ($ret as $value) {
                $street[] = ['areaname'=> $value->areaname, 'parentid'=> $ret_id, 'level'=>4];    
            }
            \app\common\models\Street::insert($street);
            $street = [];
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
