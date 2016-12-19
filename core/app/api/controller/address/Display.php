<?php
namespace app\api\controller\address;
@session_start();
use app\api\YZ;
use app\api\Request;
class Display extends YZ
{
    public function index()
    {
        global $_GPC;
        $this->_validatePara();
        $openid    = m('user')->isLogin();
        $uniacid = $_GPC["uniacid"];
        $address_id = $_GPC["address_id"];
        $total = $this->_getCount($openid,$uniacid);
        $list = $this->_getList($openid,$uniacid,$address_id);
        $this->returnSuccess(array('total' => $total, 'list' => $list));
    }
    private function _getCount($openid,$uniacid){
        $where = array(
            'openid'=>$openid,
            'uniacid'=>$uniacid,
        );
        $where[] = 'deleted=0';
        $total = D("MemberAddress")->where($where)->count();
        //echo D("MemberFavorite")->_sql();
        return $total;
    }
    private function _getList($openid,$uniacid,$address_id){
        $fields = "*";
        $where = array(
            'openid'=>$openid,
            'uniacid'=>$uniacid,
        );
        if(!empty($address_id)){
            $where['a.id'] = array('lt',$address_id);
        }
        $where[] = 'deleted=0';
        //var_dump($where);
        $list = D("MemberAddress")->alias('a')->field($fields)->where($where)
            ->order("id desc")->limit("0,10")->select();
        //echo D("MemberFavorite")->_sql();
        return $list;
    }
    private function _validatePara(){
        $validate_fields = array(
            'uniacid' => array(
                'type' => 'required',
                'describe' => '',
            ),'address_id' => array(
                'type' => 'required',
                'describe' => '手机号',
                'required' => false
            ),

        );
        Request::filter($validate_fields);
        $validate_messages = Request::validate($validate_fields);
        return $validate_messages;
    }
}

