<?php
namespace app\api\controller\supplier;
@session_start();
use app\api\YZ;

class Send extends YZ
{
    private $json;

    public function __construct()
    {
        global $_GPC;
        parent::__construct();
        $_GPC["id"] = $_GPC["orderid"];
        $result = $this->callPlugin('supplier/detail/deal/confirmsend');
        $this->json = $result;
    }
    public function index(){
        return $this->returnSuccess($this->json);
    }
}