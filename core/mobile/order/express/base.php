<?php
namespace \addoons\sz_yi\core\mobile\order\express;

class Base{
    protected $operation;
    protected $openid;
    protected $uniacid;
    protected $orderid;

    public function __construct() {
        global $_W, $_GPC;

        require_once SZ_YI_PATH.'/site.php';
        $this->site = new \Sz_yiModuleSite();
        $this->openid = m('user')->getOpenid();
        $this->uniacid = $_W['uniacid'];
        $this->orderid = intval($_GPC['id']);
        $this->shopset = m('common')->getSysset('shop');
    }

    //获取openid
    protected function getOpenid() {
        return $this->openid;
    }

    //获取微信公众号id
    protected function getUniacid() {
        return $this->uniacid;
    }

    //获取订单id
    protected function getOrderid() {
        return $this->orderid;
    }

}
