<?php

namespace app\common\services\operation;

use app\common\models\BaseModel;
use app\common\models\OperationLog;

abstract class OperationBase
{
    protected $logs;

    public $modules = '';

    public $type = '';

    public $modify_fields;

    public function __construct($model, $type)
    {
        $this->uid = intval(\YunShop::app()->uid);

        if (empty($this->uid) || is_null($type)) {
            return;
        }

        $this->setDefault();

        $this->log($model, $type);
    }

    protected function setDefault()
    {

        $this->logs['user_id'] = $this->uid;
        $this->logs['user_name'] = $this->uid;
        $this->logs['uniacid'] = \YunShop::app()->uniacid;
        $this->logs['method']  = request()->method();
        $this->logs['ip']      = $_SERVER['REMOTE_ADDR'];
        //$this->logs['input']   = json_encode(request()->all(), JSON_UNESCAPED_UNICODE); //todo 数据过大，可考虑附表
        $this->logs['modules'] = $this->modules;
        $this->logs['type'] = $this->type;
    }

    protected function log($model, $type)
    {

        if ($type == 'create') {
            $this->createLog();
        } elseif ($type == 'update') {
            $this->modifyField($model);
            $this->updateLog($model);
        }
    }


    protected function updateLog()
    {
        $fields = $this->recordField();

        $modify_fields = $this->modify_fields;

        foreach ($fields as $key => $value) {

            if ($modify_fields[$key]) {

                $this->setLog('field', $key);
                if (is_string($value)) {
                    $this->setLog('field_name', $value);
                    $this->setLog('old_content', $modify_fields[$key]['old_content']);
                    $this->setLog('new_content', $modify_fields[$key]['new_content']);
                } elseif (is_array($value)) {
                    $this->setLog('field_name', $value[$modify_fields[$key]['field_name']]);
                    $old_content = $value[$modify_fields[$key]['old_content']];
                    $new_content = $value[$modify_fields[$key]['new_content']];
                    $this->setLog('old_content', $old_content);
                    $this->setLog('new_content', $new_content);
                }
                OperationLog::create($this->logs);
            }

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




    /**
     * 设置日志值
     * @param $parameter
     * @return string
     */
    public function setLog($log, $logValue)
    {
        $this->logs[$log] = $logValue;
    }

    /**
     * 获取日志值
     * @param $parameter
     * @return string
     */
    public function getParameter($log, $logValue)
    {
        return isset($this->logs[$log])?$this->logs[$logValue] : '';
    }


    /**
     *获取所有请求的参数
     *@return array
     */
    public function getAllLogs()
    {
        return $this->logs;
    }

}