<?php
namespace app\api\controller\category;
@session_start();
use app\api\YZ;

class Index extends YZ
{
    private $json;
    private $variable;
    public function __construct()
    {
        parent::__construct();
        $result = $this->callMobile('shop/util/category');
        $this->variable = $result['variable'];
        $this->json = $result['json'];
    }
    
    public function index()
    {
        $this->json['goods_list_url'] = $this->_getGoodsListUrl();
        return $this->returnSuccess($this->json);
    }
    private function _getGoodsListUrl(){
        global $_W;
        $result = $_W['siteroot']."/app_api.php?uniacid={$_W['uniacid']}&api=goods/Display";
        return $result;
    }
}

