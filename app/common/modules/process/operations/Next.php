<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/15
 * Time: 下午3:45
 */

namespace app\common\modules\process\operations;


use app\common\modules\process\ProcessOperation;

class Next extends ProcessOperation
{
    protected function handle()
    {
        $this->process->toNextStatus();
    }
}