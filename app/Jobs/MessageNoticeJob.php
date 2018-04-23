<?php

namespace app\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class MessageNoticeJob implements  ShouldQueue
{

    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 5;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 120;

    protected $noticeModel;
    protected $templateId;
    protected $noticeData;
    protected $openId;
    protected $url;

    /**
     * Create a new job instance.
     *
     *
     */
    public function __construct($noticeModel, $templateId, $noticeData, $openId, $url)
    {
        $this->noticeModel = $noticeModel;
        $this->templateId = $templateId;
        $this->noticeData = $noticeData;
        $this->openId = $openId;
        $this->url = $url;
    }

    /**
     * Execute the job.
     *
     * @return bool
     */
    public function handle()
    {
        if ($this->attempts() > 2) {
            \Log::info('消息通知测试，执行大于两次终止');
            return true;
        }
        $this->noticeModel->uses($this->templateId)->andData($this->noticeData)->andReceiver($this->openId)->andUrl($this->url)->send();
        return true;
    }
}
