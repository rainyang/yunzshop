<?php
namespace app\api\controller\address;
@session_start();
use app\api\YZ;
use app\api\Request;
class SetDefault extends YZ
{
    public function index()
    {
        $this->_validatePara();
        $openid    = m('user')->isLogin();

        $id = Request::query('id');
        $uniacid = Request::query('uniacid');
        $data = pdo_fetch('select id from ' . tablename('sz_yi_member_address') . ' where id=:id and deleted=0 and uniacid=:uniacid limit 1', array(':uniacid' => $uniacid, ':id' => $id));
        if (empty($data)) {
            $this->returnError( '地址未找到');
        }
        pdo_update('sz_yi_member_address', array('isdefault' => 0), array('uniacid' => $uniacid, 'openid' => $openid));
        pdo_update('sz_yi_member_address', array('isdefault' => 1), array('id' => $id, 'uniacid' => $uniacid, 'openid' => $openid));
        $this->returnSuccess();
    }

    private function _validatePara(){
        $validate_fields = array(
            'uniacid' => array(
                'type' => 'required',
                'describe' => '公众号id',
            ),'id' => array(
                'type' => 'required',
                'describe' => '地址id',
                'required' => false
            ),

        );
        Request::filter($validate_fields);
        $validate_messages = Request::validate($validate_fields);
        return $validate_messages;
    }
}

