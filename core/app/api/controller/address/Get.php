<?php
namespace app\api\controller\address;
@session_start();
use app\api\YZ;
use app\api\Request;
class Get extends YZ
{
    public function index()
    {
        $this->_validatePara();
        $openid    = m('user')->isLogin();
        $member    = m('member')->getMember($openid);

        $id = Request::query('id');
        $uniacid = Request::query('uniacid');
        $data = D("MemberAddress")->where(array('uniacid' => $uniacid,'openid'=>$openid, 'id' => $id)+array('deleted=0'))->find();
        if(empty($data)){
            $this->returnError('找不到该地址');
        }
        //dump( D("MemberAddress")->_sql());
        $info = array('address' => $data, 'member' => $member);
        $this->returnSuccess($info);
    }

    private function _validatePara(){
        $validate_fields = array(
            'uniacid' => array(
                'type' => 'required',
                'describe' => '',
            ),'id' => array(
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

