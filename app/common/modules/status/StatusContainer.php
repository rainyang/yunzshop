<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/20
 * Time: 上午11:20
 */

namespace app\common\modules\status;

use app\common\modules\payType\remittance\models\status\RemittanceAuditStatus;
use app\common\modules\payType\remittance\models\status\RemittanceStatus;
use app\common\modules\process\events\AfterProcessStatusChangedEvent;
use Illuminate\Container\Container;

class StatusContainer extends Container
{

    /**
     * StatusContainer constructor.
     */
    public function __construct()
    {
        $this->setBinds();
    }

    public function handle(AfterProcessStatusChangedEvent $event)
    {

        if ($this->bound($event->getProcess()->code)) {
            $this->make($event->getProcess()->code)->handle($event->getProcess());
        }

    }

    public function setBinds()
    {
        collect([
            [
                'key' => 'remittance',
                'class' => RemittanceStatus::class,
            ], [
                'key' => 'remittanceAudit',
                'class' => RemittanceAuditStatus::class,
            ]
        ])->each(function ($item) {
            $this->bind($item['key'], function (StatusContainer $container) use ($item) {
                return new $item['class']();

            });
        });

    }
}