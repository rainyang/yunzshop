<?php

use Illuminate\Database\Seeder;

class YzpluginSeeder extends Seeder
{
    protected $table = 'yz_options';
    protected $uniTable = 'uni_account';

    public function run()
    {


        $is_plugin = \Illuminate\Support\Facades\DB::table($this->table)->where('option_name','test-plugins')->get();
        if($is_plugin->isNotEmpty())
        {
            return;
        }
        $plugins = \Illuminate\Support\Facades\DB::table($this->table)->get();
        $uniAccount = \Illuminate\Support\Facades\DB::table($this->uniTable)->get();

        $data[] = [
            'uniacid' => '0',
            'option_name' => 'test-plugins',
            'option_value' => 'true',
            'enabled' => '1',
        ];

        $data[] = [
            'uniacid' => '0',
            'option_name' => 'plugins-market',
            'option_value' => 'true',
            'enabled' => '1',
        ];
        $data[] = [
            'uniacid' => '0',
            'option_name' => 'market_source',
            'option_value' => 'http://yun.yunzshop.com/plugin.json',
            'enabled' => '1',
        ];

        foreach ($plugins as $plugin) {
            if ($plugin['option_name'] == 'plugins_enabled') {
                $plugins_enabled = $plugin['option_value'];
                continue;
            }
        }


        $i = 3;
        foreach ($uniAccount as $u) {
            foreach ($plugins as $plugin) {

                if ($plugin['option_name'] == 'plugins_enabled') {
                    continue;
                }
                if ($plugin['option_name'] == 'market_source') {
                    continue;
                }
                if ($plugin['option_name'] == 'plugins-market') {
                    continue;
                }

                $data[$i] = [
                    'uniacid' => $u['uniacid'],
                    'option_name' => $plugin['option_name'],
                    'option_value' => $plugin['option_value'],
                    'enabled' => 0,
                ];

                if (strpos($plugins_enabled, $plugin['option_name'])) {
                    $data[$i]['enabled'] = 1;
                }
                $i ++ ;
            }
        }
        \Illuminate\Support\Facades\DB::table($this->table)->delete();

        \Illuminate\Support\Facades\DB::table($this->table)->insert($data);
    }
}