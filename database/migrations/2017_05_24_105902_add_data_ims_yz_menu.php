<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use \app\common\models\Menu;

class AddDataImsYzMenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (\Schema::hasTable('yz_menu')) {

            $_menu = Menu::select('id')->where('item', 'goods.goods')->first();

            if ($_menu->id) {
                $data = [
                    [
                        'name'              => '编辑商品',
                        'item'              => 'goods.goods.edit',
                        'url'               => 'goods.goods.edit',
                        'url_params'        => '',
                        'permit'            => 1,
                        'menu'              => 1,
                        'icon'              => 'fa-circle-o',
                        'parent_id'         => $_menu->id,
                        'sort'              => 1,
                        'status'            => 1,
                        'created_at'        => time(),
                        'updated_at'        => time(),
                        'deleted_at'        => NULL
                    ],
                    [
                        'name'              => '添加商品',
                        'item'              => 'goods.goods.create',
                        'url'               => 'goods.goods.create',
                        'url_params'        => '',
                        'permit'            => 1,
                        'menu'              => 1,
                        'icon'              => 'fa-circle-o',
                        'parent_id'         => $_menu->id,
                        'sort'              => 1,
                        'status'            => 1,
                        'created_at'        => time(),
                        'updated_at'        => time(),
                        'deleted_at'        => NULL
                    ],
                    [
                        'name'              => '添加商品',
                        'item'              => 'goods.goods.destroy',
                        'url'               => 'goods.goods.destroy',
                        'url_params'        => '',
                        'permit'            => 1,
                        'menu'              => 1,
                        'icon'              => 'fa-circle-o',
                        'parent_id'         => $_menu->id,
                        'sort'              => 1,
                        'status'            => 1,
                        'created_at'        => time(),
                        'updated_at'        => time(),
                        'deleted_at'        => NULL
                    ],
                    [
                        'name'              => '复制商品',
                        'item'              => 'goods.goods.copy',
                        'url'               => 'goods.goods.copy',
                        'url_params'        => '',
                        'permit'            => 1,
                        'menu'              => 1,
                        'icon'              => 'fa-circle-o',
                        'parent_id'         => $_menu->id,
                        'sort'              => 1,
                        'status'            => 1,
                        'created_at'        => time(),
                        'updated_at'        => time(),
                        'deleted_at'        => NULL
                    ]
                ];
                Menu::insert($data);
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
