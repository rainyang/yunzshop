<?php
namespace app\api\controller\favorite;
@session_start();
use app\api\YZ;
use app\api\Request;

/**
 * 收藏商品API
 * URL传参: 公众号uniacid, 商品id
 * 如果根据商品id没有查到数据, 将返回show_json(0, '商品未找到'); 如果能够查到该商品, 而且之前并未收藏, 将写入数据库
 */
class Set extends YZ
{
    private $json;
    private $variable;

    public function __construct()
    {
        parent::__construct();
        global $_W;
        $_W['ispost']= true; //不需要这个指令?
        $result = $this->callMobile('shop/favorite/set');
        //dump($result);exit;
        // ddump($result);
        // if($result['code'] == -1){
        //     $this->returnError($result['json']);
        // }
        $this->variable = $result['variable'];
        $this->json = $result['json'];
    }

    public function index()
    {
        $this->returnSuccess($this->json,'收藏成功');
    }

}

