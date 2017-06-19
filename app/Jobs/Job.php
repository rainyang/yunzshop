<?php

namespace app\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Redis;
use Yunshop\SingleReturn\models\TestQuery;

class Job implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    protected $key;
    protected $value;

    public $tries = 5;
    public $timeout = 120;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($key, $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data = [
            'key' => $this->key,
            'value' => $this->value,
        ];
        TestQuery::insert($data);

    }
}
