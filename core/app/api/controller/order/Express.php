<?php
namespace app\api\controller\order;
@session_start();
use app\api\YZ;
use app\api\Request;

class Express extends YZ
{
    private $json;
    private $variable;

    public function __construct()
    {

        parent::__construct();
        global $_W;
        $_W['ispost']= true;
        $result = $this->callMobile('order/express/display');
        if($result['code'] == -1){
            $this->returnError();
        }
        $this->variable = $result['variable'];
        $this->json = $result['json'];
    }

    public function index()
    {
        $res = $this->json;
        $res['express_list'] = getExpress($res['order']['express'],$res['order']['expresssn']);
        $this->returnSuccess($res);
    }
}