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
    //获取推荐分类
    private function _getCategory()
    {
        global $_W;
        $category = set_medias(pdo_fetchall('SELECT id, name, thumb,level FROM '. tablename('sz_yi_category') . ' WHERE isrecommand = 1 AND uniacid= '.$_W['uniacid'].' ORDER BY displayorder, id DESC'),'thumb');

        return $category;
    }

    //获取推荐商品
    private function _getRecommand()
    {

        $goods = m('goods')->getList(array('pagesize' => 100000, 'isrecommand' => 1));

        return $goods;
    }

    public function index(){
        $res['goods'] = $this->_getGoods();
        $res['ads'] = m('shop')->getADs();
        dump($res['ads']);
        $res['category'] = $this->_getCategory();
        $res['recommand'] = $this->_getRecommand();
        $this->returnSuccess($res);
    }
}