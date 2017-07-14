<?php

namespace app\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class UpdateVersion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:version {version}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '应用更新';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->createPluginFile();
        $this->runMigrate();
    }

    public function runMigrate()
    {
        \Artisan::call('migrate',['--force' => true]);
        //更新数据表
        $versionMigration = 'database/migrations/' . $username = $this->argument('version');
        if(is_dir(base_path($versionMigration) )){
            \Artisan::call('migrate',['--force' => true,'--path' => $versionMigration]);
        }
    }

    public function createPluginFile()
    {
        $pluginFile = base_path('../../web') . '/plugin.php';
        if(!file_exists($pluginFile)){
            file_put_contents($pluginFile,"<?php
 
define('IN_IA', true);


include_once __DIR__ . '/../addons/yun_shop/app/laravel.php';

include_once __DIR__ . '/../addons/yun_shop/app/yunshop.php';
");
        }
    }
}
