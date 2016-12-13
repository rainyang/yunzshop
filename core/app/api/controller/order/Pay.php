<?php
namespace app\api\controller\order;
@session_start();
use app\api\YZ;
use app\api\Request;

class Pay extends YZ
{
    private $json;
    private $variable;

    public function __construct()
    {
        parent::__construct();

    }
    public function display()
    {

    }
    public function confirm()
    {

    }
    public function index()
    {
        global $_W;
        $_W['ispost']= true;
        //$result = $this->callMobile('order/history/display');
        $result = $this->callMobile('order/pay/pay');

        //dump($result);exit;
        if($result['code'] == -1){
            $this->returnError($result['json']);
        }
        $this->variable = $result['variable'];
        $this->json = $result['json'];

        $this->returnSuccess($this->json);
    }
    public function isPay(){
        global $_GPC,$_W;
        $result   = pdo_fetch('select status,goodsprice,address from ' . tablename('sz_yi_order') . ' where id=:id  and uniacid=:uniacid limit 1', array(
            ':uniacid' => $_W['uniacid'],
            ':id' => $_GPC['order_id']
        ));
        dump(pdo_sql_debug('select status,goodsprice,address from ' . tablename('sz_yi_order') . ' where id=:id  and uniacid=:uniacid limit 1', array(
            ':uniacid' => $_W['uniacid'],
            ':id' => $_GPC['order_id']
        )));
        if($result['status']<1){
            $this->returnError('未付款');
        }
        $result['address'] = unserialize($result['address']);
        $this->returnSuccess($result,'付款成功');
    }
    /**
     * 余额支付
     *
     * @method post
     * @request member/Pay/credit
     * @param orderid
     * @param type
     *
     */
    public function credit()
    {
        global $_W;
        $_W['ispost']= true;

        $result = $this->callMobile('order/pay/complete');

        if($result['code'] == -1){
            $this->returnError($result['json']);
        }
        $this->variable = $result['variable'];
        $this->json = $result['json'];

        $this->returnSuccess($this->json);

    }
}

