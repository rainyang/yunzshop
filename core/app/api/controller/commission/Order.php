<?php
namespace app\api\controller\commission;
@session_start();
use app\api\YZ;
use yii\helpers\ArrayHelper;

class Order extends YZ
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $result = $this->callPlugin('commission/order');
        $result['json']['set']['texts']['order'] = $result['json']['set']['texts']['order'] . "(" . $result['json']['ordercount'] . ")";
        $result['json']['set']['texts']['commission_yj'] = "预计: +" . $result['json']['commissioncount'] . "元";
        $result['json']['set']['texts']['commission'] = "预计" . $result['json']['set']['texts']['commission'];
        $pageno = $result['json']['ordercount']/$result['json']['pagesize'];   
        $result['json']['pageno'] = $pageno ? ceil($pageno) : 0;
        foreach ($result['json']['list'] as $key => &$val) {
        	$val['ordersn'] = $val['ordersn'] . "(" . $val['level'] . "级）";
            $val['commission'] = "+" . $val['commission'];
            $val['weixin'] = "微信号: " . $val['weixin'];
            foreach ($val['order_goods'] as &$god) {
                $god['optionname'] = $god['optionname'] . "x" . $god['total'];
                $god['commission'] = "+" . $god['commission'];
            }
        }
        $result['json']['navs'] = array(
            '0' => array("text" => "所有订单", "status" => ""),
            '1' => array("text" => "待付款", "status" => "0"),
            '2' => array("text" => "已付款", "status" => "1"),
            '3' => array("text" => "已完成", "status" => "3")
            );
        $this->returnSuccess($result);
    }
}