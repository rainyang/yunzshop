<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/20
 * Time: 上午11:20
 */

namespace app\common\modules\status;

use app\common\models\Status;
use app\common\modules\payType\remittance\models\status\RemittanceAuditPassed;
use app\common\modules\payType\remittance\models\status\RemittanceWaitReceipt;
use app\common\modules\process\events\AfterProcessStatusChangedEvent;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Event;

class StatusContainer extends Container
{
    public function handle(AfterProcessStatusChangedEvent $event){
        $this->make($event->getProcess()->status->code,$event->getProcess()->status)->handle();

    }
    /**
     * StatusContainer constructor.
     */
    public function __construct()
    {
        $this->setBinds();
    }

    public function setBinds()
    {
        collect([
            [
                'key' => 'remittance.waitReceipt',
                'class' => RemittanceWaitReceipt::class,
            ],[
                'key' => 'remittanceAudit.passed',
                'class' => RemittanceAuditPassed::class,
            ],
        ])->each(function ($item) {
            $this->bind($item['key'], function (StatusContainer $container, Status $status) use ($item) {
                return new $item['class']($status);

            });
        });

    }
}