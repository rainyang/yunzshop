<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/18
 * Time: 下午8:58
 */

namespace app\backend\modules\audit;


use app\backend\models\Process;
use app\common\components\BaseController;

class DetailController extends BaseController
{
    protected function getProcess(){
        if(!isset($this->process)){
            $processId = request()->input('process_id');

            $this->process = Process::find($processId);
        }
        return $this->process;
    }
    public function index()
    {
        dd($this->getProcess());
    }
}