<?php

/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/3/9
 * Time: 上午9:54
 */
use Illuminate\Database\Seeder;

class YzStreetSeeder extends Seeder
{
    protected $oldTable = 'sz_yi_street';
    protected $newTable = 'yz_street';
    public function run()
    {
        $newList = DB::table($this->newTable)->get();
        if($newList->isNotEmpty()){
            echo "yz_street 已经有数据了跳过\n";
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
    }

}