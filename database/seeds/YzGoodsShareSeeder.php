<?php

/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/3/9
 * Time: 上午9:54
 */
use Illuminate\Database\Seeder;
class YzGoodsShareSeeder extends Seeder
{
    protected $oldTable = 'sz_yi_goods';
    protected $newTable = 'yz_goods_share';
    
    public function run()
    {
        $newList = DB::table($this->newTable)->get();
        if($newList->isNotEmpty()){
            echo "yz_goods_share 已经有数据了跳过\n";
            return ;
        }
        $list =  DB::table($this->oldTable)->get();
        if($list) {
            foreach ($list as $v) {
                DB::table($this->newTable)->insert([
                    'goods_id'=> $v['id'],
                    'need_follow'=> $v['needfollow'],
                    'no_follow_message'=> $v['followtip'],
                    'follow_message'=> $v['followurl'],
                    'share_title'=> $v['share_title'],
                    'share_thumb'=> $v['share_icon'],
                    'share_desc'=> $v['description'],
                ]);

            }
        }
    }

}