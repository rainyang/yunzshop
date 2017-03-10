<?php

/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/3/9
 * Time: 上午9:54
 */
use Illuminate\Database\Seeder;
use app\common\models\Member;
class YzNoticeSeeder extends Seeder
{
    protected $oldTable = 'sz_yi_goods';
    protected $newTable = 'yz_goods_notices';
    
    public function run()
    {
        $newList = DB::table($this->newTable)->get();
        if($newList->isNotEmpty()){
            echo "yz_goods_notices 已经有数据了跳过\n";
            return ;
        }
        $list =  DB::table($this->oldTable)->get();
        if($list) {
            foreach ($list as $v) {
                $uid = Member::getUidByOpenID($v['noticeopenid']);
                $noticetype = explode(",", $v['noticetype']);
                foreach ($noticetype as $item) {
                    DB::table($this->newTable)->insert([
                        'goods_id'=> $v['id'],
                        'uid'=> $uid,
                        'type'=> $item,
                    ]);
                }

            }
        }
    }

}