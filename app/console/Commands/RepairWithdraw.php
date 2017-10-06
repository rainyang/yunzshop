<?php

namespace app\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class RepairWithdraw extends Command
{

    protected $signature = 'fix:income';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '修复收入';

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
        $bar = $this->output->createProgressBar(100);

        for($i = 0; $i<100;$i++) {
            $this->info('test success'.$i.'!');
            sleep(1);
            $bar->advance();
        }
        $bar->finish();
        $this->error('test fail!');
        $this->comment('test comment!');
    }

}
