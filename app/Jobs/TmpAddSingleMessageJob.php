<?php

namespace app\Jobs;

use EasyWeChat\Support\Log;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Yunshop\SingleReturn\services\MessageService;

class TmpAddSingleMessageJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    protected $noticeData;
    protected $memberFans;
    protected $uniacid;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($noticeData, $memberFans, $uniacid)
    {
        $this->noticeData = $noticeData;
        $this->memberFans = $memberFans;
        $this->uniacid = $uniacid;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        MessageService::orderReturnRueue($this->noticeData, $this->memberFans, $this->uniacid);
    }
}
