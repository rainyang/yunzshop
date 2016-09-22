<?php
namespace app\api\controller\index;
use app\api\Request;
use app\api\YZ;
class Index extends YZ
{

    public function getGoodsList()
    {
        //$para = Request::all();
        //$type = $_GPC['type'];
        $goodsid = Request::input('goodsid');
        $keywords = Request::input('keywords','');
        $args = array('page' => 1,'pagesize' => 10,'goodsid' => $goodsid,'keywords'=>$keywords ,'isrecommand' => 1, 'order' => 'displayorder desc,id desc', 'by' => '');

        $goods = m('goods')->getList($args);
        foreach ($goods as &$good){
            $good = array_part('id,thumb,title,marketprice,type,groupnumber,productprice,productprice',$good);
        }
        $this->returnSuccess($goods);
    }
}