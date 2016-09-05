<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
load()->func('communication');
$openid = m('user')->getOpenid();
$member = m('member')->getInfo($openid);
$key    = 'sxhsz201608';
if ($_W['isajax']) {
    if ($_W['ispost']) {
        $memberdata = $_GPC['memberdata'];
		$url = 'http://api-test-monitor.shanxinhui.com/User/UserApi/user_bind';    
		$post_data = array(               
		"bindname" => $memberdata['bindname'],
		"pwd" => $memberdata['pwd'],
		"password" =>md5($memberdata['bindname'].$key),
		"timestamp" => date('YmdHis',time()),
		);
		$output = ihttp_request($url,$post_data, null, 1);
		$output = json_decode($output['content'],true);
		if ($output['status']==0) {   
			pdo_update('sz_yi_member', array('is_bind' =>'1'), array(
	           'openid' => $openid,
	           'uniacid' => $_W['uniacid']
	        ));
	        $bind = array(
	           'uniacid' => $_W['uniacid'],
	           'userid' =>$member['id'],
	           'username' =>$output['user']['username'],
	           'realname' =>$output['user']['realname'],
	           'userscore' =>$output['user']['userscore'],
	           'mobile' =>$output['user']['mobile'],
	           'idcard' =>$output['user']['idcard'],
	           'verify' =>$output['user']['verify'],
	           'bindtime' =>date('Y-m-d H:i:s',time()),
	        );
	        pdo_insert('sz_yi_sxh_member', $bind);
			$bindid = pdo_insertid();
			if($bindid){
				show_json(0, array(
		        'msg' =>'绑定成功'
		   		));
			}

		}else{
			show_json(1, array(
		        'msg' => $output['msg']
		    ));
		}
        
     
    }
    show_json(1, array(
        'member' => $member
    ));
}

include $this->template('member/bind');
