<?php

/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/3/9
 * Time: 上午9:54
 */
use Illuminate\Database\Seeder;

class YzAddressSeeder extends Seeder
{
    protected $oldTable = 'sz_yi_address';
    protected $newTable = 'yz_address';
    
    public function run()
    {
        $newList = DB::table($this->newTable)->get();
        if($newList->isNotEmpty()){
            echo "yz_address 已经有数据了跳过\n";
            return ;
        }
        $list =  DB::table($this->oldTable)->get();
        if($list) {
            foreach ($list as $v) {
                DB::table($this->newTable)->insert([
                    'id'=>$v['id'],
                    'areaname'=>$v['areaname'],
                    'parentid'=>$v['parentid'],
                    'level'=>$v['level']
                ]);
            }
        }

        // TODO: Implement run() method.
    }

}