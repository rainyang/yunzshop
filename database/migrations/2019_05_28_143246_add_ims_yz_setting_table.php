<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddImsYzSettingTable extends Migration
{
    protected $table = 'yz_setting';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (Schema::hasTable($this->table)) {

            dd(\Setting::get('shop.shop'), \YunShop::app());

            if (!\app\common\models\DispatchType::where('name','酒店入住')->count()) {
                \app\common\models\DispatchType::where('id', 4)->delete();
                \Illuminate\Support\Facades\DB::insert('INSERT INTO `'.app('db')->getTablePrefix().'yz_dispatch_type` (`id`, `name`, `plugin`, `need_send`)
VALUES
	(4, \'酒店入住\', 33, 1);
');
            }
        }


        if (!Schema::hasTable($this->table)) {
            echo $this->table." 不存在 跳过\n";
            return;
        }

        $table = DB::table($this->table)->where('key', 'global')->first();
        if($table){
            // 已经有数据了跳过
            echo $this->table." There's already data skipped.\n";
            return ;
        }

        $config['image_extentions'] = ['0' => 'gif', '1' => 'jpg', '2' => 'jpeg', '3' => 'png'];
        $config['image_limit'] = 5000;
        $config['audio_extentions'] = ['0' => 'mp3', '1' => 'mp4'];
        $config['audio_limit'] = 5000;
        $config['thumb_width'] = 800;
        $config['zip_percentage'] = 100;

        DB::table($this->table)->insert([
            'key' => 'global',
            'value' => serialize($config),
            'created_at' => time(),
            'updated_at' => time()
        ]);


        Schema::create('', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('yz_setting');
    }
}
