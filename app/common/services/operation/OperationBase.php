<?php

namespace app\common\services\operation;

use app\common\models\BaseModel;
use app\common\models\OperationLog;
use app\common\models\user\User;

abstract class OperationBase
{
    //基础模型
    protected $model;

    //需要记录的参数数组
    protected $logs;

    public $modules = '';

    public $type = '';

    public $modify_fields = [];

    /**
     * @param $model mixed
     * @param $type string 判断记录类型
    */
    public function __construct($model, $type)
    {
        $this->uid = intval(\YunShop::app()->uid);

        if (empty($this->uid) || is_null($type)) {
            return;
        }

        $this->model = $model;

        $this->setDefault();

        $this->log($type);
    }

    protected function setDefault()
    {
        $user_model = User::where('uid', $this->uid)->first();
        if ($user_model) {
            $this->logs['user_name'] = $user_model->username;
        }
        $this->logs['user_id'] = $this->uid;
        $this->logs['uniacid'] = \YunShop::app()->uniacid;
        $this->logs['method']  = request()->method();
        $this->logs['ip']      = $_SERVER['REMOTE_ADDR'];
        $this->logs['modules'] = $this->modules;
        $this->logs['type'] = $this->type;
        //$this->logs['input']   = json_encode(request()->all(), JSON_UNESCAPED_UNICODE); //todo 数据过大，可考虑附表
    }

    protected function log($type)
    {

        $this->modifyField();
        if ($type == 'create') {
            $this->createLog();
        } elseif ($type == 'update') {
            $this->updateLog();
        }
    }


    protected function updateLog()
    {
        $fields = $this->recordField();

        $modify_fields = $this->modifyField();

        //没有修改的值，不记录
        if (empty($modify_fields)) {
            OperationLog::create($this->logs);
            return true;
        }

        foreach ($fields as $key => $value) {

            if ($modify_fields[$key]) {

                $this->setLog('field', $key);
                if (is_string($value)) {
                    $this->setLog('field_name', $value);
                    $this->setLog('old_content', $modify_fields[$key]['old_content']);
                    $this->setLog('new_content', $modify_fields[$key]['new_content']);
                } elseif (is_array($value)) {
                    $this->setLog('field_name', $value['field_name']);
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
        $fields = $this->recordField();

        $modify_fields = $this->modifyField();

        //没有值，记录失败
        if (empty($modify_fields)) {
            $this->setLog('extend', '创建模型的记录失败');
            $this->setLog('status', 1);
            $this->setLog('input', json_encode($this->model, JSON_UNESCAPED_UNICODE));
            OperationLog::create($this->logs);
            return true;
        }

        foreach ($fields as $key => $value) {

            if ($modify_fields[$key]) {

                $this->setLog('field', $key);
                if (is_string($value)) {
                    $this->setLog('field_name', $value);
                    $this->setLog('old_content', $modify_fields[$key]['old_content']);
                    $this->setLog('new_content', $modify_fields[$key]['new_content']);
                } elseif (is_array($value)) {
                    $this->setLog('field_name', $value['field_name']);
                    $old_content = $value[$modify_fields[$key]['old_content']];
                    $new_content = $value[$modify_fields[$key]['new_content']];
                    $this->setLog('old_content', $old_content);
                    $this->setLog('new_content', $new_content);
                }
                OperationLog::create($this->logs);
            }

        }

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
    abstract protected function modifyField();




    /**
     * 设置日志值
     * @param $log
     * @param $logValue
     * @return string
     */
    public function setLog($log, $logValue)
    {
        $this->logs[$log] = $logValue;
    }

    /**
     * 获取日志值
     * @param $log
     * @return string
     */
    public function getParameter($log)
    {
        return isset($this->logs[$log])?$this->logs[$log] : '';
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