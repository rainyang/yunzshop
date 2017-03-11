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
        

        \DB::table('yz_options')->delete();
        
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