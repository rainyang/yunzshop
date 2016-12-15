<?php
namespace app\api\controller\goods;
@session_start();
use app\api\YZ;
use app\api\Request;
class Detail extends YZ
{
    private $json;
    private $variable;

    public function __construct()
    {
        parent::__construct();
        global $_W;
        $_W['ispost']= true;
        $result = $this->callMobile('shop/detail');
        //dump($result);exit;
        if($result['code'] == -1){
            $this->returnError($result['json']);
        }
        $this->variable = $result['variable'];
        $this->json = $result['json'];
    }
    private function _getShareInfo()
    {
        global $_W;
        $result = array(
            'title' => $_W['shopshare']['title'],
            'webUrl' => $_W['shopshare']['link'] . '&access=app',
            'imageUrl' => $_W['shopshare']['imgUrl'],
            'content' => $_W['shopshare']['desc']
        );
        return $result;
    }
    public function index()
    {
        $res = $this->json;
        $res['share'] = $this->_getShareInfo();

        $this->returnSuccess($res);
    }
    public function _goodsPrice(){
        /*<%if goods.minprice !=goods.maxprice%>
                <%goods.minprice%> - <%goods.maxprice%>
                <%else%>
                <%goods.marketprice%>
                <%/if%>*/
    }
}

