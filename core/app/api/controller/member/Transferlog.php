<?php
/**
 * Created by PhpStorm.
 * User: rayyang
 * Date: 16/9/30
 * Time: 下午5:39
 */
namespace app\api\controller\member;
@session_start();

use app\api\YZ;

class Transferlog extends YZ
{
    private $_json_datas;

    public function __construct()
    {
        parent::__construct();

    }

    public function index()
    {
        $result = $this->callMobile('member/transfer_log');
        foreach ($result['json']['list'] as $key => &$value) {
            $value['status_name'] = '';
            if ($value['type'] == '1') {
                if ($value['status'] == '1') {
                    $value['status_name'] = "转让成功";
                }
            } else {
                if ($value['status'] == '1') {
                    $value['status_name'] = "受让成功";
                }
            }
        }
        unset($value);
        $this->returnSuccess($result);


    }

}