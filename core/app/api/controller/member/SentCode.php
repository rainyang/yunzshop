<?php
namespace app\api\controller\member;
@session_start();
use app\api\Request;
use app\api\YZ;

class SentCode extends YZ
{
    private $json;
    private $variable;

    public function __construct()
    {
        parent::__construct();

    }

    public function index()
    {
        $result = $this->callMobile('member/sendcode/sendcode');
        $this->returnSuccess($result['json']);
    }
}