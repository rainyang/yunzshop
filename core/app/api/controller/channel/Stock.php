<?php
namespace app\api\controller\channel;
@session_start();
use app\api\YZ;

class Stock extends YZ
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $result = $this->callPlugin('channel/stock');
        $this->returnSuccess($result);
    }
}
