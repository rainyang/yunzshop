<?php

namespace app\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class MessageNoticeJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    protected $noticeModel;
    protected $templateId;
    protected $noticeData;
    protected $openId;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($noticeModel,$templateId,$noticeData,$openId)
    {
        $this->noticeModel = $noticeModel;
        $this->templateId = $templateId;
        $this->noticeData = $noticeData;
        $this->openId = $openId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->noticeModel->uses($this->templateId)->andData($this->noticeData)->andReceiver($this->openId)->send();
    }
}
