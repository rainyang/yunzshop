<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        //配置
        $this->call(SettingSeeder::class);
        //权限
        $this->call(YzPermissionSeeder::class);
        //用户角色
        $this->call(YzUserRoleSeeder::class);

    }
}
