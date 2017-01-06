<?php
namespace app\api\controller\order;
@session_start();
use app\api\YZ;

class Operation extends YZ
{
    private $json;

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        //$a = Str::startsWith("","/");
        //dump($a);exit;
        global $_W;
        $button_id = $_GET['button_id'];
        if (in_array($button_id, array(Order::IN_REFUND, Order::IN_AFTER_SALE))) {
            $r = new Refund();
            $r->display();
        }
        if (in_array($button_id, array())) {
            $_W['ispost'] = true;
        }
        $route = Order::getButtonApi($button_id);
        $result = $this->callMobile($route);
        if($button_id == Order::PAY){
            //订单抵扣到价格为0时,开启余额支付,关闭微信支付
            if($result['json']['order']['price'] == 0){
                $result['json']['free']['success'] = true;
                $result['json']['app_wechat']['success'] = false;

            }
        }
        if ($result['status'] == -1) {
            $this->returnError($result['json']);
        }
        //dump($result);exit;
        $this->json = $result['json'];
        $this->returnSuccess($this->json);
    }
}

