<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 16/9/27
 * Time: 下午7:54
 */
namespace app\api\controller\member;
@session_start();

use app\api\YZ;

class Account extends YZ
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $result = $this->callMobile('member/center');

        $res = array();

        $this->returnSuccess($result);
    }
}