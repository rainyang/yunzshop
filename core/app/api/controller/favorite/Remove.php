<?php
namespace app\api\controller\favirite;
@session_start();
use app\api\YZ;
use app\api\Request;

class Remove extends YZ
{
    public function index()
    {
        $openid = m('user')->isLogin();
        $id = Request::input('id');
        $uniacid = Request::query('uniacid');

        $data = pdo_fetch('select id,isdefault from ' . tablename('sz_yi_member_address') . ' where  id=:id and openid=:openid and deleted=0 and uniacid=:uniacid  limit 1', array(':uniacid' => $uniacid, ':openid' => $openid, ':id' => $id));
        if (empty($data)) {
            $this->returnError('地址未找到');
        }
        pdo_update('sz_yi_member_address', array('deleted' => 1), array('id' => $id));
        if ($data['isdefault'] == 1) {
            pdo_update('sz_yi_member_address', array('isdefault' => 0), array('uniacid' => $uniacid, 'openid' => $openid, 'id' => $id));
            $data2 = pdo_fetch('select id from ' . tablename('sz_yi_member_address') . ' where openid=:openid and deleted=0 and uniacid=:uniacid order by id desc limit 1', array(':uniacid' => $uniacid, ':openid' => $openid));
            if (!empty($data2)) {
                pdo_update('sz_yi_member_address', array('isdefault' => 1), array('uniacid' => $uniacid, 'openid' => $openid, 'id' => $data2['id']));
                $this->returnSuccess(array('defaultid' => $data2['id']));
            }
        }
        $this->returnSuccess();
    }

}

