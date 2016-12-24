<?php
namespace app\api\controller\commission;
@session_start();
use app\api\YZ;

class Customer extends YZ
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $result = $this->callPlugin('commission/Customer');
        foreach ($result['json']['list'] as $key => &$val) {
        	$val['avatar'] = !empty($val['avatar']) ? $val['avatar'] : "../addons/sz_yi/plugin/commission/images/head.jpg";
        	$val['nickname'] = !empty($val['nickname']) ? $val['nickname'] : "未获取";
        }
        $this->returnSuccess($result);
    }
}