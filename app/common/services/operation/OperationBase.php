<?php

namespace app\common\services\operation;

use app\common\models\BaseModel;
use app\common\models\OperationLog;

abstract class OperationBase
{
    protected $log;

    public function __construct($model, $type = null)
    {
        if (empty(\YunShop::app()->uid) || is_null($type)) {
            return;
        }
        $this->log['user_id'] = \YunShop::app()->uid;
        $this->log['method']  = request()->method();
        $this->log['ip']      = $_SERVER['REMOTE_ADDR'];
        $this->log['input']   = json_encode(request()->all(), JSON_UNESCAPED_UNICODE);

        $this->log($model, $type);
    }

    protected function log($model, $type)
    {

        if ($type == 'create') {
            $this->createLog();
        } elseif ($type == 'update') {
            $this->updateLog($model);
        }
    }


    protected function updateLog($model)
    {
        $fields = $this->recordField();

        $modify_fields = $this->modifyField($model);

        foreach ($fields as $key => $value) {



            OperationLog::create($this->log);
        }

    }

    protected function createLog()
    {

        OperationLog::create($this->log);
    }

    /**
     * 获取模型需要记录的字段
     * @return mixed
     */
    abstract protected function recordField();

    /**
     * 获取模型修改了哪些字段
     * @param object array
     * @return array
     */
    abstract protected function modifyField($model);

}