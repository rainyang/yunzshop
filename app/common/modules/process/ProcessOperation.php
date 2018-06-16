<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/15
 * Time: 下午3:09
 */

namespace app\common\modules\process;

use app\common\models\Process;

/**
 * 流程操作类
 * Class ProcessOperation
 * @package app\common\modules\process
 */
abstract class ProcessOperation
{
    /**
     * @var Process
     */
    protected $process;

    public function __construct(Process $process)
    {
        $this->process = $process;
    }

    protected function execute()
    {
        $this->handle();
    }

    abstract protected function handle();
}