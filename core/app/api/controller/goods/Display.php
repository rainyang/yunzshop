<?php
namespace app\api\controller\goods;
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
        global $_W,$_GPC;
        $_W['ispost'] = true;
        $_GPC['pagesize'] = 10;
        $result = $this->callMobile('shop/list');
        //dump($result);exit;
        if ($result['code'] == -1) {
            $this->returnError($result['json']);
        }
        $this->variable = $result['variable'];
        $this->json = $result['json'];
    }

    public function index()
    {
        global $_GPC;
        $res = $this->json;
        foreach ($res['goods'] as &$good) {
            unset($good['content']);
        }
        $res['page'] = $_GPC['page'] ?: 1;
        $this->returnSuccess($res);
    }

}

