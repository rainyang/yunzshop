<?php

use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    protected $oldTable = 'sz_yi_sysset';
    protected $newTable = 'yz_setting';
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       $newList = DB::table($this->newTable)->get();
       if($newList->isNotEmpty()){
          echo "yz_setting 已经有数据了跳过\n";
          return ;
       }

       $list =  DB::table($this->oldTable)->get();
       if($list){
           foreach ($list as $v){

               \YunShop::app()->uniacid = $v['uniacid'];
                if($v['sets']) {
                    $sets = unserialize($v['sets']);
                    if($sets) {
                        foreach ($sets as $k1 => $v1) {
                            Setting::set('shop.' . $k1, $v1);
                        }
                    }
                }
                if($v['plugins']) {
                    $plugins = unserialize($v['plugins']);
                    if($plugins) {
                        foreach ($plugins as $k2 => $v2) {
                            if(is_array($v2)) {
                                foreach ($v2 as $kk2 => $vv2) {
                                    Setting::set(($k2 ? : 'plugin') . '.' . $kk2, $vv2);
                                }
                            }else{
                                Setting::set('plugin.' . $k2, $v2);
                            }
                        }
                    }
                }
               if($v['sec']) {
                   $sec = unserialize($v['sec']);
                   if($sec) {
                       foreach ($sec as $k3 => $v3) {
                           Setting::set('pay.' . $k3, $v3);
                       }
                   }
               }
               echo "完成：uniacid:".$v['uniacid'] ."\n";
           }
       }
    }
}
