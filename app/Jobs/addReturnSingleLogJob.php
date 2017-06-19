<?php

namespace app\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Config;
use Yunshop\SingleReturn\models\returnSingleLog;
use Yunshop\SingleReturn\services\TimedTaskReturnService;

class addReturnSingleLogJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    protected $returnSingleLogData;
    protected $config;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($returnSingleLogData)
    {
        $this->returnSingleLogData = $returnSingleLogData;
        $this->config = Config::get('income.singleReturn');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $logId = returnSingleLog::insertGetId($this->returnSingleLogData);
        $incomeData = [
            'uniacid' => $this->returnSingleLogData['uniacid'],
            'member_id' => $this->returnSingleLogData['uid'],
            'incometable_type' => $this->config['class'],
            'incometable_id' => $logId,
            'type_name' => $this->config['title'],
            'amount' => $this->returnSingleLogData['amount'],
            'status' => 0,
            'pay_status' => 0,
            'create_month' => date('Y-m'),
            'created_at' => time()
        ];
        (new TimedTaskReturnService())->addIncome($incomeData);


    }
}
