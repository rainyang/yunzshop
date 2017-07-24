<?php

namespace app\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendMessage implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    protected $data = null;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle($i)
    {
        Log::info("队列**********:".$i);
        //file_put_contents("")
    }
}
