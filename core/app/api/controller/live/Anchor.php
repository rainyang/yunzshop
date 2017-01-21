<?php
namespace app\api\controller\live;
@session_start();
use app\api\YZ;
use app\api\Request;

/**
 * 返回直播间列表
 */
class Anchor extends YZ
{
    
    public function index()
    {
        global $_W, $_GPC;
        $cloud_anchor_id = $_GPC['anchor_id'];

        $openid = pdo_fetchcolumn('SELECT openid FROM ' . tablename('sz_yi_live_anchor') . ' WHERE cloud_anchor_id = :cloud_anchor_id', array(':cloud_anchor_id' => $cloud_anchor_id));

        $member_info = pdo_fetch('SELECT nickname, avatar FROM ' . tablename('sz_yi_member') . ' WHERE openid = :openid', array(':openid' => $openid));

        if(!empty($member_info)){
            $this->returnSuccess($member_info);
        } else {
            $this->returnError('获取信息失败');
        }
    }
    
}

