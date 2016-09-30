<?php
namespace app\api\controller\returnmoney;
@session_start();
use app\api\YZ;
use yii\helpers\ArrayHelper;

class Index extends YZ
{
    private $json;
    private $variable;

    public function __construct()
    {
        parent::__construct();

    }

    public function index()
    {
        $result = $this->callPlugin('return/return_log');

        foreach ($result['json']['list'] as $key => &$value) {
            if ($value['status'] == 1) {
                $value['status_name'] = "已完成";
            } else {
                $value['status_name'] = "失败";
            } 
        }
        unset($value);
        $this->returnSuccess($result);
    }

    public function queue()
    {
        $result = $this->callPlugin('return/return_queue');
echo "<pre>";print_r($result);exit;
        foreach ($result['json']['list'] as $key => &$value) {
            if ($value['status'] == 1) {
                $value['status_name'] = "已完成";
            } else {
                $value['status_name'] = "失败";
            } 
        }
        unset($value);
        $this->returnSuccess($result);
    }
 
}