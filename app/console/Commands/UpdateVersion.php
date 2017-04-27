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
        //更新数据表
        $versionMigration = 'database/migrations/' . $username = $this->argument('version');
        if(is_dir(base_path($versionMigration) )){
            \Artisan::call('migrate',['--force' => true,'--path' => $versionMigration]);
        }
    }
}
