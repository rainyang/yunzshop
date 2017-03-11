<?php

/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/3/10
 * Time: 下午2:21
 */
use Illuminate\Database\Seeder;

class YzCategorySeeder extends Seeder
{
    protected $oldTable = 'sz_yi_category';
    protected $newTable = 'yz_category';

    public function run()
    {
        $newList = DB::table($this->newTable)->get();
        if($newList->isNotEmpty()){
            echo "yz_category 已经有数据了跳过\n";
            return ;
        }
        $list =  DB::table($this->oldTable)->get();
        if($list) {
            foreach ($list as $v) {
                DB::table($this->newTable)->insert([
                    'id'=> $v['id'],
                    'uniacid'=> $v['uniacid'],
                    'name'=> $v['name'],
                    'thumb'=> $v['thumb'],
                    'parent_id'=> $v['parentid'],
                    'description'=> $v['description'],
                    'display_order'=> $v['displayorder'],
                    'enabled'=> $v['enabled'],
                    'is_home'=> $v['ishome'],
                    'adv_img'=> $v['advimg'],
                    'adv_url'=> $v['advurl'],
                    'level'=> $v['level'],
                    'created_at'=> time(),
                    'updated_at'=> NULL,
                    'deleted_at'=> NULL
                ]);
            }
        }

    }
}