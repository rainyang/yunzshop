<?php
namespace app\api\controller\category;
@session_start();
use app\api\YZ;

class Display extends YZ
{
    private $json;
    private $variable;
    public function __construct()
    {
        parent::__construct();
        $result = $this->callMobile('shop/list');  
        $this->variable = $result['variable'];
        $this->json = $result['json'];
        //is_new=1 新上宝贝
        //isrecommand =1推荐宝贝
        // ishot=1 热销
        // istime=1 限时
        // isdiscount=1 促销
    }
    
    public function index()
    {  
        return $this->returnSuccess($this->json);
    }
}

