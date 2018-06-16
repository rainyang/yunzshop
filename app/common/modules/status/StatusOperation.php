<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/15
 * Time: 下午3:09
 */

namespace app\common\modules\status;
use app\common\models\Status;


/**
 * 流程操作类
 * Class StatusOperation
 * @package app\common\modules\process
 */
abstract class StatusOperation
{
    /**
     * @var Status
     */
    protected $status;

    public function __construct(Status $status)
    {
        $this->status = $status;
    }

    protected function execute()
    {
        $this->handle();
    }

    abstract protected function handle();
}