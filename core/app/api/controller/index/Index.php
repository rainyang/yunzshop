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
        $openid = m('user')->isLogin();
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

    //获取一级菜单
    private function _getCategory()
    {
        $category = set_medias(pdo_fetchall('SELECT name, advimg FROM '. tablename('sz_yi_category') . ' WHERE parentid = 0 ORDER BY displayorder, id DESC'),'advimg,thumb');
        return $category;
    }

    //获取推荐商品
    private function _getRecommand()
    {
        $recommand = set_medias(pdo_fetchall('SELECT id, title, thumb, productprice, marketprice FROM '. tablename('sz_yi_goods') . ' WHERE isrecommand = 1 and deleted = 0 ORDER BY displayorder, id DESC LIMIT 10'),'thumb');
        return $recommand;
    }

    public function index(){
        $res['goods'] = $this->_getGoods();
        $res['ads'] = m('shop')->getADs();
        $res['category'] = $this->_getCategory();
        $res['recommand'] = $this->_getRecommand();
        $this->returnSuccess($res);
    }
}