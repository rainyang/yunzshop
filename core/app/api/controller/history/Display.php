<?php
namespace app\api\controller\history;
@session_start();
use app\api\YZ;
use app\api\Request;
class Display extends YZ
{
    private $json;
    private $variable;

    public function __construct()
    {
        parent::__construct();
        global $_W;
        $_W['ispost']= true;
        $result = $this->callMobile('shop/history/display');
        //dump($result);exit;
        if($result['code'] == -1){
            $this->returnError($result['json']);
        }
        $this->variable = $result['variable'];
        $this->json = $result['json'];
    }

    public function index()
    {
        $this->returnSuccess($this->json);
    }
}