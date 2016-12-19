<?php
namespace app\api\controller\returnmoney;
@session_start();
use app\api\YZ;

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
        foreach ($result['json']['list'] as $key => &$value) {

            if ($result['json']['type'] == '0') {
                if ( ($value['money'] - $value['return_money']) == '0') {
                    $value['status_name'] = "已完成";
                } else {
                    $value['status_name'] = "剩余金额".($value['money'] - $value['return_money'])." 元";
                }   
            } elseif ($result['json']['type'] == '2') {
                if ( $value['status'] == '0') {
                    $value['status_name'] = "等待返现";
                } else {
                    $value['status_name'] = "已返现";
                } 
            } elseif ($result['json']['type'] == '3') {
                if ( $value['status'] == '1') {
                    $value['status_name'] = "已完成";
                } else {
                    $value['status_name'] = "进行中";
                } 
            }


        }
        unset($value);
        $this->returnSuccess($result);
    }
 
}