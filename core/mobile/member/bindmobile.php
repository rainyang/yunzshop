<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$openid = m('user')->getOpenid();
$member = m('member')->getMember($openid);
$preUrl = $_COOKIE['preUrl'];
if(is_weixin()){
    //是否强制绑定手机号,只针对微信端
    $setdata = pdo_fetch("select * from " . tablename('sz_yi_sysset') . ' where uniacid=:uniacid limit 1', array(
            ':uniacid' => $_W['uniacid']
        ));
    $set     = unserialize($setdata['sets']);
    if(!empty($set['shop']['isbindmobile'])){
        $nobindmobile_hide = true;
    }
}
if ($_W['isajax']) {
    if ($_W['ispost']) {
        $mc = $_GPC['memberdata'];
         $memberall = pdo_fetchall('select * from ' . tablename('sz_yi_member') . ' where  mobile =:mobile and openid!=:openid and uniacid=:uniacid', array(':uniacid' => $_W['uniacid'], ':openid' => $openid, ':mobile' => $mc['mobile']));

        if($memberall){
            foreach ($memberall as $key => $info) {
                $oldopenid = $info['openid'];
                $prem = array('openid' => $oldopenid, 'uniacid' => $_W['uniacid']);
                $sql_tabs = array('sz_yi_member_address', 'sz_yi_member_cart', 'sz_yi_member_history', 'sz_yi_member_favorite', 'sz_yi_member_log', 'sz_yi_order', 'sz_yi_order_comment', 'sz_yi_saler', 'sz_yi_coupon_data', 'sz_yi_coupon_guess', 'sz_yi_coupon_log', 'sz_yi_creditshop_log', 'sz_yi_feedback', 'sz_yi_goods_comment','sz_yi_order_goods');
                foreach ($sql_tabs as $val) {
                    pdo_update($val, array('openid' => $openid), $prem);
                }
                //update原有pc的地址及订单等记录的openid都为新的微信openid
                /*pdo_update('sz_yi_member_address', array('openid' => $openid), $prem);
                pdo_update('sz_yi_member_cart', array('openid' => $openid), $prem);
                pdo_update('sz_yi_member_history', array('openid' => $openid), $prem);
                pdo_update('sz_yi_member_favorite', array('openid' => $openid), $prem);
                pdo_update('sz_yi_member_log', array('openid' => $openid), $prem);
                pdo_update('sz_yi_order', array('openid' => $openid), $prem);
                pdo_update('sz_yi_order_comment', array('openid' => $openid), $prem);
                pdo_update('sz_yi_saler', array('openid' => $openid), $prem);
                pdo_update('sz_yi_coupon_data', array('openid' => $openid), $prem);
                pdo_update('sz_yi_coupon_guess', array('openid' => $openid), $prem);
                pdo_update('sz_yi_coupon_log', array('openid' => $openid), array('openid' => $oldopenid));
                pdo_update('sz_yi_creditshop_log', array('openid' => $openid), array('openid' => $oldopenid));

                pdo_update('sz_yi_commission_clickcount', array('openid' => $openid), array('openid' => $oldopenid));*/
                //commission_clickcount里面有from_openid不知什么意思，回头看下

                //删除pc的手机号
                pdo_delete('sz_yi_member', array('openid' => $oldopenid));
                $mc_member = pdo_fetch('select * from ' . tablename('mc_mapping_fans') . ' where openid=:openid and uniacid=:uniacid', array(':uniacid' => $_W['uniacid'], ':openid' => $oldopenid));
                if(!empty($mc_member)){
                    pdo_delete('mc_mapping_fans', array('openid' => $oldopenid));
                    if(!empty($mc_member['uid'])){
                       pdo_delete('mc_members', array('uid' => $mc_member['uid'])); 
                    }
                }

                //更新微信记录里的手机号等为pc的手机号
                $member = pdo_fetch('select mobile, pwd, credit1, credit2 from ' . tablename('sz_yi_member') . ' where openid=:openid and uniacid=:uniacid', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
                $data = array('isbindmobile' => 1);
                if($member['mobile']!=$mc['mobile'] || empty($member['mobile'])){
                    $data['mobile'] = $mc['mobile'];
                }
                if(empty($member['pwd']) && !empty($mc['password'])){
                    $data['pwd'] = md5($mc['password']);
                }else if(empty($member['pwd']) && !empty($info['pwd'])){
                    $data['pwd'] = $info['pwd'];
                }

                if($info['credit1'] > 0){
                    $data['credit1'] = $member['credit1'] + $info['credit1'];
                }
                if($info['credit2'] > 0){
                    $data['credit2'] = $member['credit2'] + $info['credit2'];
                }
                pdo_update('sz_yi_member', $data, array('openid' => $openid, 'uniacid' => $_W['uniacid']));
            }
            
            
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
                array(
                    'openid' => $openid, 
                    'uniacid' => $_W['uniacid']
                ));
            show_json(1, array(
                'preurl' => $preUrl
            ));
        }
    }
}
include $this->template('member/bindmobile');
