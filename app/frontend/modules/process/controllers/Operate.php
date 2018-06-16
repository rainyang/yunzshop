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
     * @var Process
     */
    protected $process;

    protected function getProcess(){
        if(!isset($this->process)){
            $processId = request()->input('process_id');

            $this->process = Process::find($processId);
        }
        return $this->process;
    }
    public function next()
    {
        $data = $this->getProcess()->toNextState();
        return $data;
    }
}