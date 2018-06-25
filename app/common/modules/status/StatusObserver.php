<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/22
 * Time: 下午5:22
 */

namespace app\common\modules\status;


use app\common\models\Process;
use app\common\models\Status;

class StatusObserver
{
    /**
     * @var Status
     */
    protected $status;

    public function __construct(Status $status)
    {
        $this->status = $status;
    }


}