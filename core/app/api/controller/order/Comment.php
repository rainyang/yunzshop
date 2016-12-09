<?php
namespace app\api\controller\order;
@session_start();
use app\api\YZ;
use app\api\Request;

class Comment extends YZ
{
    private $json;
    private $variable;

    public function __construct()
    {

        parent::__construct();
        global $_W,$_GPC;
        $_W['ispost']= true;
        $_GPC['comments'] = json_decode($_GPC['comments'],true);
        $result = $this->callMobile('order/op/comment');
        //dump($result);exit;
        if($result['code'] == -1){
            $this->returnError($result['json']);
        }
        $this->variable = $result['variable'];
        $this->json = $result['json'];
    }

    public function index()
    {
        $res = $this->json;
        //$res['order'] = array_part('expresssn,expresscom',$this->json);
        $this->returnSuccess($res);
    }
}