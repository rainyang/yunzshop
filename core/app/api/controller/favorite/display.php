<?php
namespace app\api\controller\favorite;
@session_start();
use app\api\YZ;
use app\api\Request;
class Display extends YZ
{
    public function index()
    {
        $this->_validatePara();
        $openid    = m('user')->isLogin();
        $uniacid = Request::input("uniacid");
        $favorite_id = Request::input("favorite_id");//Request::input("favorite_id");
        $total = $this->_getCount($openid,$uniacid);
        $list = $this->_getList($openid,$uniacid,$favorite_id);

        $this->returnSuccess(array('total' => $total, 'list' => $list));

    }
    private function _getCount($openid,$uniacid){
        $where = array(
            //'openid'=>$openid,
            'uniacid'=>$uniacid,
        );
        $where[] = 'deleted=0';
        $total = D("MemberFavorite")->where($where)->count();
        //echo D("MemberFavorite")->_sql();
        return $total;
    }
    private function _getList($openid,$uniacid,$favorite_id){
        $fields = "f.id,f.goodsid,g.title,g.thumb,g.marketprice,g.productprice";
        $where = array(
            //'openid'=>$openid,
            'f.uniacid'=>$uniacid,
        );
        if(!empty($favorite_id)){
            $where['f.id'] = array('lt',$favorite_id);
        }
        $where[] = 'f.deleted=0';
        $list = D("MemberFavorite")->alias('f')->field($fields)->where($where)
            ->join(tablename('sz_yi_goods')." as g ON g.id = f.goodsid")->order("f.id desc")->limit("0,10")->select();
        //dump(D("MemberFavorite")->_sql());
        $list = set_medias($list, 'thumb');
        //echo D("MemberFavorite")->_sql();
        return $list;
    }
    private function _validatePara(){
        $validate_fields = array(
            'uniacid' => array(
                'type' => 'required',
                'describe' => '手机号',
            ),'favorite_id' => array(
                'type' => 'required',
                'describe' => '收藏id',
                'required' => false
            ),

        );
        Request::filter($validate_fields);
        $validate_messages = Request::validate($validate_fields);
        return $validate_messages;
    }
}

