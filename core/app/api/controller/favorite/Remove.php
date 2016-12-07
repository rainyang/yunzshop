<?php
namespace app\api\controller\favorite;
@session_start();
use app\api\YZ;
use app\api\Request;

class Remove extends YZ
{
    private $json;
    private $variable;

    public function __construct()
    {
        parent::__construct();

    }

    public function index()
    {
        //goods_ids
        global $_W;

        $_W['ispost']= true;

        $result = $this->callMobile('shop/favorite/remove');
        //dump($result);exit;
        if($result['code'] == -1){
            $this->returnError($result['json']);
        }
        $this->variable = $result['variable'];
        $this->json = $result['json'];
        $this->returnSuccess($this->json,'取消收藏');
    }
}

