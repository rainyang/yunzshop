<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$openid = m('user')->getOpenid();

$preUrl = $_COOKIE['preUrl'];
if ($_W['isajax']) {
    if ($_W['ispost']) {
        $mc = $_GPC['memberdata'];
         $info = pdo_fetch('select * from ' . tablename('sz_yi_member') . ' where  mobile ="'.$mc['mobile'].'" and pwd <> ""');

        if($info){
            $oldopenid = $info['openid'];
            //update原有pc的地址及订单等记录的openid都为新的微信openid
			pdo_update('sz_yi_member_address', array('openid' => $openid), array('openid' => $oldopenid));
			pdo_update('sz_yi_member_cart', array('openid' => $openid), array('openid' => $oldopenid));
			pdo_update('sz_yi_member_favorite', array('openid' => $openid), array('openid' => $oldopenid));
			pdo_update('sz_yi_member_log', array('openid' => $openid), array('openid' => $oldopenid));
			pdo_update('sz_yi_order', array('openid' => $openid), array('openid' => $oldopenid));
			pdo_update('sz_yi_order_comment', array('openid' => $openid), array('openid' => $oldopenid));
			pdo_update('sz_yi_saler', array('openid' => $openid), array('openid' => $oldopenid));
			pdo_update('sz_yi_coupon_data', array('openid' => $openid), array('openid' => $oldopenid));
			pdo_update('sz_yi_coupon_guess', array('openid' => $openid), array('openid' => $oldopenid));
			pdo_update('sz_yi_coupon_log', array('openid' => $openid), array('openid' => $oldopenid));
			pdo_update('sz_yi_creditshop_log', array('openid' => $openid), array('openid' => $oldopenid));
            //commission_clickcount里面有from_openid不知什么意思，回头看下

            //删除pc的手机号
			pdo_delete('sz_yi_member', array('openid' => $oldopenid));

            //更新微信记录里的手机号等为pc的手机号
            pdo_update('sz_yi_member', 
                array(
                    'mobile' => $info['mobile'], 
                    'pwd' => $info['pwd'], 
                    'isbindmobile' => 1,
                    'credit1' => $info['credit1'], 
                    'credit2' => $info['credit2'], 
                ), 
                array('openid' => $openid)
            );
            
            show_json(1, array(
                'preurl' => $preUrl
            ));
        }
        else{
            //更新微信记录里的手机号等
            pdo_update('sz_yi_member', 
                array(
                    'mobile' => $mc['mobile'], 
                    'pwd' => md5($mc['password']), 
                    'isbindmobile' => 1,
                ), 
                array('openid' => $openid)
            );
            show_json(1, array(
                'preurl' => $preUrl
            ));
        }
    }
}
include $this->template('member/bindmobile');
