<?php

use Illuminate\Database\Seeder;

class YzOptionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        $newList = DB::table('yz_options')->get();
        if($newList->isNotEmpty()){
            echo "yz_options 已经有数据了跳过\n";
            return ;
        }

        \DB::table('yz_options')->insert(array (
            0 => 
            array (
                'id' => 1,
                'option_name' => 'example-plugin',
                'option_value' => 'true',
            ),
            1 => 
            array (
                'id' => 2,
                'option_name' => 'plugins_enabled',
                'option_value' => '["example-plugin"]',
            ),
        ));
        
        
    }
}