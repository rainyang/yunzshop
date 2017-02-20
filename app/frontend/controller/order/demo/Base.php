<?php
namespace app\frontend\controller\order\demo;
class Demo{
    protected $openid;
    protected $uniacid;
    protected $orderid;
    protected $core;

    public function __construct()
    {
        global $_W, $_GPC;
        require_once SZ_YI_INC.'core.php';

        $this->core = new \Core();
        //require_once SZ_YI_INC.'util/Debug.php';
        $this->openid = m("user")->getOpenid();
        $this->uniacid = $_W["uniacid"];
        $this->orderid = intval($_GPC["id"]);
        $this->shopset = m('common')->getSysset('shop');

    }
}