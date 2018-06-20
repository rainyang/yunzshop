<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/16
 * Time: 上午10:53
 */

namespace app\frontend\modules\process\controllers;


use app\frontend\models\Process;

trait Operate
{
    /**
     * @return Process
     */
    abstract protected function getProcess();

    /**
     * @return \Illuminate\Database\Eloquent\Model
     * @throws \Exception
     */
    public function toNextState()
    {
        $data = $this->getProcess()->toNextStatus();
        return $data;
    }
}