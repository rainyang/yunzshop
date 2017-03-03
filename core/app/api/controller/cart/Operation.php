<?php
namespace app\api\controller\cart;
@session_start();
use app\api\YZ;

class Operation extends YZ
{
    private $json;

    public function __construct()
    {

        parent::__construct();
        //cartids=14,13


        //$this->variable = $result['variable'];
    }
    private function _getIds($ids){
        if(is_numeric($ids)){
            return array($ids);
        }else{
            $ids = explode(',',$ids);
        }
        return $ids;
    }
    public function index()
    {

    }
    public function add(){
        global $_W;

        $_W['ispost'] = true;
        $result = $this->callMobile('shop/cart/add');
        $this->json = $result['json'];

        $this->returnSuccess($this->json);
    }
    public function remove(){
        global $_GPC,$_W;
        $_W['ispost'] = true;
        $_GPC['ids'] = $this->_getIds($_GPC['ids']);
        $result = $this->callMobile('shop/cart/remove');
        $this->json = $result['json'];

        $this->returnSuccess($this->json);
    }
}

