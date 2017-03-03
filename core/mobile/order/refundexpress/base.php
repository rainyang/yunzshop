<?php
namespace mobile\order;
class Base{
    protected $operation;
    protected $openid;
    protected $uniacid;
    protected $orderid;

    public function __construct()
    {
        global $_W, $_GPC;


        $this->operation = !empty($_GPC["op"]) ? $_GPC["op"] : "display";
        $this->openid = m("user")->getOpenid();
        $this->uniacid = $_W["uniacid"];
        $this->orderid = intval($_GPC["id"]);
    }
    protected function getOpenid(){
        return $this->openid;
    }
    protected function getUniacid(){
        return $this->uniacid;

    }
    protected function getOrderId(){
        return $this->orderid;
    }
    protected function sortByTime($a, $b)
    {
        if ($a["ts"] == $b["ts"]) {
            return 0;
        } else {
            return $a["ts"] > $b["ts"] ? 1 : -1;
        }
    }
}