<?php
/**
 * Created by PhpStorm.
 * User: rayyang
 * Date: 16/10/08
 * Time: 下午2:30
 */
namespace app\api\controller\member;
@session_start();

use app\api\YZ;

class Log extends YZ
{
    private $_json_datas;

    public function __construct()
    {
        parent::__construct();

    }

    public function index()
    {
        $result = $this->callMobile('member/log');
        foreach ($result['json']['list'] as $key => &$value) {
            if ($value['status'] == '0') {
                if ($value['type'] == '0') {
                    $value['status_name'] = "未充值";
                }else{
                    $value['status_name'] = "申请中";
                }
            } elseif ($value['status'] == '1') {
                if ($value['type'] == '0') {
                    $value['status_name'] = "充值成功";
                }elseif ($value['type'] == '1') {
                    $value['status_name'] = "提现成功";
                }else{
                    $value['status_name'] = "打款成功";
                }
            } elseif ($value['status'] == '-1') {
                if ($value['type'] == '1') {
                    $value['status_name'] = "提现失败";
                }
            } elseif ($value['status'] == '3') {
                if ($value['type'] == '0') {
                    $value['status_name'] = "充值退款";
                } 
            }
        }
        $this->returnSuccess($result);


    }

}