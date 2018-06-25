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