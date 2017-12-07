<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class EditAddressAndStreet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $area = \app\common\models\Address::select()->where('areaname', '新吴区')->where('parentid', 320200)->whereLevel(3)->first();
        $parent_id = 0;
        if ($area) {
            $parent_id = $area->id;
            $area->delete();
            \app\common\models\Street::select()->where('parentid', $parent_id)->whereLevel(4)->delete();
        }
        \app\common\models\Address::insertGetId([
            'id'       => 320214,
            'areaname' => '新吴区',
            'parentid' => 320200,
            'level'    => 3
        ]);
        \app\common\models\Street::insert([
            ['id' => 320214055, 'areaname' => '鸿山', 'parentid' => 320214, 'level' => 4],
            ['id' => 320214052, 'areaname' => '江溪', 'parentid' => 320214, 'level' => 4],
            ['id' => 320214050, 'areaname' => '旺庄', 'parentid' => 320214, 'level' => 4],
            ['id' => 320214051, 'areaname' => '硕放', 'parentid' => 320214, 'level' => 4],
            ['id' => 320214054, 'areaname' => '梅村', 'parentid' => 320214, 'level' => 4],
            ['id' => 320214053, 'areaname' => '新安', 'parentid' => 320214, 'level' => 4],
        ]);
        if ($parent_id) {
            if (Schema::hasTable('yz_plugin_store_self_delivery')) {
                $self_deliverys = \Illuminate\Support\Facades\DB::table('yz_plugin_store_self_delivery')->select()->where('district_id',
                    $parent_id)->get();
                if (!$self_deliverys->isEmpty()) {
                    $self_deliverys->each(function($item){
                        DB::table('yz_plugin_store_self_delivery')->whereId($item['id'])->update(['district_id' => 320214]);
                    });
                }
            }
            if (Schema::hasTable('yz_plugin_store_store_delivery')) {
                $store_deliverys = \Illuminate\Support\Facades\DB::table('yz_plugin_store_store_delivery')->select()->where('district_id',
                    $parent_id)->get();
                if (!$store_deliverys->isEmpty()) {
                    $store_deliverys->each(function($item){
                        DB::table('yz_plugin_store_store_delivery')->whereId($item['id'])->update(['district_id' => 320214]);
                    });
                }
            }
            if (Schema::hasTable('yz_order_address')) {
                $order_address = \Illuminate\Support\Facades\DB::table('yz_order_address')->select()->where('district_id',
                    $parent_id)->get();
                if (!$order_address->isEmpty()) {
                    $order_address->each(function($item){
                        DB::table('yz_order_address')->whereId($item['id'])->update(['district_id' => 320214]);
                    });
                }
            }
            if (Schema::hasTable('yz_store')) {
                $stores = \Illuminate\Support\Facades\DB::table('yz_store')->select()->where('district_id',
                    $parent_id)->get();
                if (!$stores->isEmpty()) {
                    $stores->each(function($item){
                        DB::table('yz_store')->whereId($item['id'])->update(['district_id' => 320214]);
                    });
                }
            }
        }

        //ims_yz_plugin_store_self_delivery  ims_yz_plugin_store_store_delivery ims_yz_order_address  ims_yz_shop_address ims_yz_store
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
