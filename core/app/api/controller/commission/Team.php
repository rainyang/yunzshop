<?php
namespace app\api\controller\commission;
@session_start();
use app\api\YZ;

class Team extends YZ
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $result = $this->callPlugin('commission/team');
        $result['json']['set']['texts']['myteam'] = $result['json']['set']['texts']['myteam'] . "(" . $result['json']['total'] . ")";
        $result['json']['set']['texts']['c1'] = $result['json']['set']['texts']['c1'] . "(" . $result['json']['level1'] . ")";
		$result['json']['set']['texts']['c2'] = $result['json']['set']['texts']['c2'] . "(" . $result['json']['level2'] . ")";
		$result['json']['set']['texts']['c3'] = $result['json']['set']['texts']['c3'] . "(" . $result['json']['level3'] . ")";
		$result['json']['set']['texts']['commission_team'] = "TA的" . $result['json']['set']['texts']['commission'] . "/成员";
        $result['json']['pageon'] = ceil($result['json']['total'] / $result['json']['pagesize']);
        foreach ($result['json']['list'] as $key => &$val) {
        	$val['avatar'] = !empty($val['avatar']) ? $val['avatar'] : "../addons/sz_yi/plugin/commission/images/head.jpg";
        	$val['nickname'] = !empty($val['nickname']) ? $val['nickname'] : "未获取";
        	$val['commission_total'] = "+" . $val['commission_total'];
        	$val['agentcount'] = $val['agentcount'] . "个成员";
        }
        $this->returnSuccess($result);
    }
}