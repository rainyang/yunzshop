<?php
namespace app\api\controller\live;
@session_start();
use app\api\YZ;
use app\api\Request;

/**
 * 返回直播间关联商品的详情
 * @parms num room_id
 * 
 */
class Goods extends YZ
{
    
    public function index()
    {
        global $_W, $_GPC;
        $room_id = $_GPC['room_id'];
        $page = $_GPC['page'];
        $page_size = $_GPC['pagesize'];

        load()->func('communication');
        $url = 'http://sy.yunzshop.com/admin_live.php?api=room/Goods&room_id=' . $room_id;
        if(!empty($page)){
            $url .= '&page=' . $page;
        }
        if(!empty($page_size)){
            $url .= '&pagesize=' . $page_size;
        }
        $result = ihttp_get($url);
        $result_array = json_decode($result['content'], true);
        $goods_list = $result_array['data']['list'];

        if(!empty($goods_list)){

            //获取商品ID
            $scope = array();
            foreach($goods_list as $k=>$v){
                $scope[$k] = $v['goods_id'];
            }
            $scope = implode(',', $scope);

            //查询商品详细信息
            $goods_info_list = pdo_fetchall('SELECT id, thumb, title, productprice, marketprice FROM ' . tablename('sz_yi_goods') . ' WHERE id IN (' . $scope . ')');
            $goods_info_list = set_medias($goods_info_list, "thumb");
        }

        if(!empty($goods_info_list)){
            $this->returnSuccess($goods_info_list);
        } else {
            $this->returnError('获取失败');
        }
    }
}
