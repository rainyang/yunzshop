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
        print_r($result);exit();
        $result['json']['set']['texts']['commission_detail'] = $result['json']['set']['texts']['commission_detail'] . "(" . $result['json']['total'] . ")";
        $result['json']['set']['texts']['commission_yj'] = "预计".$result['json']['set']['texts']['commission'] . "：+" . $result['json']['commissioncount'] . "元";
        $commission_sq = "申请".$result['json']['set']['texts']['commission'];
        foreach ($result['json']['list'] as $key => &$val) {
        	$val['applyno'] = "编号：" . $val['applyno'];
            $val['commission_sq'] = $commission_sq . "：" . $val['commission'];
            switch ($val['status']) {
                case 1:
                    $val['commission_sq'] .= " 申请时间：" . $val['dealtime'];
                    break;
                case 2:
                    $val['commission_sq'] .= " 审核时间：" . $val['dealtime'];
                    break;
                case 3:
                    $val['commission_sq'] .= " 打款时间：" . $val['dealtime'];
                    break;
                case -1:
                    $val['commission_sq'] .= " 无效时间：" . $val['dealtime'];
                    break;
                default:
                    break;
            }
        	$val['commission_pay'] = "+" . $val['commission_pay'];
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