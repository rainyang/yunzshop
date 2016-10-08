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
        $this->returnSuccess($result);


    }

}