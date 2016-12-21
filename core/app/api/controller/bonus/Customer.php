<?php
namespace app\api\controller\bonus;
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
        $result = $this->callPlugin('bonus/Customer');
        $result['json']['set']['texts']['myteam'] = $result['json']['set']['texts']['mycustomer'] . "(" . $result['json']['total'] . ")";
        $result['json']['set']['texts']['member'] = "会员信息";
        $result['json']['set']['texts']['commission_team'] = "TA的消费订单/金额";
        foreach ($result['json']['list'] as $key => &$val) {
            $val['avatar'] = !empty($val['avatar']) ? $val['avatar'] : "../addons/sz_yi/plugin/commission/images/head.jpg";
            $val['nickname'] = !empty($val['nickname']) ? $val['nickname'] : "未获取";
            $val['createtime'] = "注册时间：" . $val['createtime'];
            $val['moneycount'] = number_format($val['moneycount'], 2) . " 元";
            $val['ordercount'] = $val['ordercount'] . " 订单";
        }
        $this->returnSuccess($result);
    }
}