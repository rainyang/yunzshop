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

    /**
     * 余额支付
     *
     * @method post
     * @request order/Pay/credit
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

