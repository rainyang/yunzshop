<?php
namespace app\api\controller\verify;
@session_start();
use app\api\YZ;
use app\api\Request;
class Qrcode extends YZ
{
    private $json;
    private $variable;

    public function __construct()
    {
        parent::__construct();
        global $_W;
        $result = $this->callPlugin('verify/Qrcode');
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