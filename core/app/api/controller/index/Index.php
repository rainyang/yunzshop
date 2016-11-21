<?php
namespace app\api\controller\index;
use app\api\Request;
use app\api\YZ;
class Index extends YZ
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
    private function _getGoods()
    {
        $res = $this->json;
        foreach ($res['goods'] as &$good) {
            unset($good['content']);
        }
        return $res['goods'];
    }
    public function index(){
        $res['goods'] = $this->_getGoods();
        $res['ads'] = m('shop')->getADs();
        $this->returnSuccess($res);
    }
}