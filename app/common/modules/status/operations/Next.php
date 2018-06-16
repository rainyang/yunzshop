<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/15
 * Time: 下午3:45
 */

namespace app\common\modules\status\operations;


use app\common\modules\status\StatusOperation;

class Next extends StatusOperation
{
    protected function handle()
    {
        $this->status->toNextState();
    }
}