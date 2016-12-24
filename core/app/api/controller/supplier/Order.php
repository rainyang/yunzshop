<?php
namespace app\api\controller\supplier;
@session_start();
use app\api\YZ;

class Order extends YZ
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $result = $this->callPlugin('supplier/orderj/order');
        $result['json']['navs'] = array(
            '0' => array("text" => "全部", "status" => ""),
            '1' => array("text" => "待付款", "status" => "0"),
            '2' => array("text" => "已付款", "status" => "1"),
            '3' => array("text" => "已完成", "status" => "3")
        );
        $this->returnSuccess($result);
    }
}