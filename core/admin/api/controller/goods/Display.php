<?php
/**
 * 管理后台APP API订单接口
 *
 * PHP version 5.6.15
 *
 * @package   订单模块
 * @author    shenyang <shenyang@yunzshop.com>
 * @version   v1.0
 */
namespace admin\api\controller\goods;
class Display extends \admin\api\YZ
{
    //private $order_info;
    public function __construct()
    {
        parent::__construct();
        //$api->validate('username','password');
    }

    public function index()
    {
        $para = $this->getPara();

        $this->ca('shop.goods.view');
        $goods_list = $this->_getGoodsList($para);
        if (empty($goods_list)) {
            $this->returnSuccess($goods_list, '暂无数据');
        }
        //$pager = pagination($total, $pindex, $psize);
        dump($goods_list);
        $this->returnSuccess($goods_list);
    }

    public function getCateTree()
    {
        $para = $this->getPara();
        $goods_model = new \admin\api\model\goods();
        $cate_tree = $goods_model->getCateTree($para['uniacid']);
        dump($cate_tree);
        $this->returnSuccess($cate_tree);
    }

    private function _getGoodsList($para)
    {
        $goods_model = new \admin\api\model\goods();
        $fields = 'title,thumb,marketprice,total,sales,id as goods_id,status';
        $goods_list = $goods_model->getList(array(
            'id' => $para["goods_id"],
            'uniacid' => $para["uniacid"],
            'status' => $para["status"],
            'uid' => $para["uid"],
            'keyword' => $para["keyword"],
            'pcate' => $para["pcate"],
            'ccate' => $para["ccate"],

        ), $fields);
        return $goods_list;
    }
}