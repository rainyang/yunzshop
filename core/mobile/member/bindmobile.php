<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$openid = m('user')->getOpenid();
$member = m('member')->getMember($openid);
$preUrl = $_COOKIE['preUrl'];
if (is_weixin()) {
    // 是否强制绑定手机号,只针对微信端
    $setdata = pdo_fetch("select * from ".tablename('sz_yi_sysset').' where uniacid=:uniacid limit 1', array(
            ':uniacid' => $_W['uniacid']
        ));
    $set     = unserialize($setdata['sets']);
    if (!empty($set['shop']['isbindmobile'])) {
        $nobindmobile_hide = true;
    }
}
if ($_W['isajax']) {
    if ($_W['ispost']) {
        $mc = $_GPC['memberdata'];
        //查询其它已绑定该手机的微信号
        $isbindmobile = pdo_fetchcolumn('select count(*) from ' . tablename('sz_yi_member') . ' where  mobile =:mobile and uniacid=:uniacid and isbindmobile=1', array(':uniacid' => $_W['uniacid'], ':mobile' => $mc['mobile']));
        if(!empty($isbindmobile)){
            show_json(0, array());
        }
        
        //更换公众号或pc到微信绑定
        $memberall = pdo_fetchall('select id, openid, pwd, level, agentlevel, bonuslevel, createtime, bindapp, status, isagent, agentid from ' . tablename('sz_yi_member') . ' where  mobile =:mobile and openid!=:openid and uniacid=:uniacid', array(':uniacid' => $_W['uniacid'], ':openid' => $openid, ':mobile' => $mc['mobile']));
        $log = "绑定开始++++++++++++++++++++++++++++++++";
        $log .= print_r($member, 1);
        $log .= print_r($memberall, 1);
        if (!empty($memberall)) {
            foreach ($memberall as $key => $info) {
                $oldopenid = $info['openid'];
                $prem = array('openid' => $oldopenid, 'uniacid' => $_W['uniacid']);
                $sql_tabs = array(
                    'sz_yi_member_address',
                    'sz_yi_member_cart',
                    'sz_yi_member_history',
                    'sz_yi_member_favorite',
                    'sz_yi_member_log',
                    'sz_yi_order',
                    'sz_yi_order_comment',
                    'sz_yi_saler',
                    'sz_yi_coupon_data',
                    'sz_yi_coupon_guess',
                    'sz_yi_coupon_log',
                    'sz_yi_creditshop_log',
                    'sz_yi_feedback',
                    'sz_yi_goods_comment',
                    'sz_yi_order_goods'
                );
                foreach ($sql_tabs as $val) {
                    pdo_update($val, array('openid' => $openid), $prem);
                }

                //更新微信记录里的手机号等为pc的手机号
                $member = pdo_fetch('select id, mobile, pwd, credit1, credit2, level, agentlevel, bonuslevel, createtime, bindapp, status, isagent from ' . tablename('sz_yi_member') . ' where openid=:openid and uniacid=:uniacid', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
                $data = array('isbindmobile' => 1);
                if ($member['mobile'] != $mc['mobile'] || !empty($mc['mobile'])) {
                    $data['mobile'] = $mc['mobile'];
                }
                if (!empty($mc['password'])) {
                    $data['pwd'] = md5($mc['password']);
                } elseif (empty($member['pwd']) && !empty($info['pwd'])) {
                    $data['pwd'] = $info['pwd'];
                }
                //获取积分
                $credit1 = m('member')->getCredit($oldopenid, 'credit1');
                if ($credit1 > 0) {
                    m('member')->setCredit($openid, 'credit1', $credit1, array(0, '会员绑定积分合并，合并过来的积分为：' . $credit1 . " 积分"));
                }
                //获取余额
                $credit2 = m('member')->getCredit($oldopenid, 'credit2');
                if ($credit2 > 0) {
                    m('member')->setCredit($openid, 'credit2', $credit2, array(0, '会员绑定余额合并，合并过来的余额为：' . $credit2 . " 元"));
                }

                //会员等级对比
                if(!empty($info['level'])){
                    $newlevel = "";
                    //查询其它会员等级的权重
                    $oldlevel = pdo_fetchcolumn('select level from ' . tablename('sz_yi_member_level') . ' where id=:id and uniacid=:uniacid', array(':uniacid' => $_W['uniacid'], ':id' => $info['level']));
                    //如当前会员有等级查询当前会员等级权重
                    if(!empty($member['level'])){
                        $newlevel = pdo_fetchcolumn('select level from ' . tablename('sz_yi_member_level') . ' where id=:id and uniacid=:uniacid', array(':uniacid' => $_W['uniacid'], ':id' => $member['level']));
                    }
                    if(empty($newlevel) || $oldlevel > $newlevel){
                       $data['level'] = $info['level'];
                    }
                }

                //分销等级对比
                if(!empty($info['agentlevel'])){
                    $newagentlevel = "";
                    $oldagentlevel = pdo_fetchcolumn('select level from ' . tablename('sz_yi_commission_level') . ' where id=:id and uniacid=:uniacid', array(':uniacid' => $_W['uniacid'], ':id' => $info['agentlevel']));
                    if(!empty($member['agentlevel'])){
                        $newagentlevel = pdo_fetchcolumn('select level from ' . tablename('sz_yi_commission_level') . ' where id=:id and uniacid=:uniacid', array(':uniacid' => $_W['uniacid'], ':id' => $member['agentlevel']));
                    }
                    if(empty($newagentlevel) || $oldagentlevel > $newagentlevel){
                       $data['agentlevel'] = $info['agentlevel'];
                    }
                }

                //代理等级对比
                if(!empty($info['bonuslevel'])){
                    $newbonuslevel = "";
                    $oldbonuslevel = pdo_fetchcolumn('select level from ' . tablename('sz_yi_bonus_level') . ' where id=:id and uniacid=:uniacid', array(':uniacid' => $_W['uniacid'], ':id' => $info['bonuslevel']));
                    if(!empty($member['bonuslevel'])){
                        $newbonuslevel = pdo_fetchcolumn('select level from ' . tablename('sz_yi_bonus_level') . ' where id=:id and uniacid=:uniacid', array(':uniacid' => $_W['uniacid'], ':id' => $member['bonuslevel']));
                    }
                    if(empty($newbonuslevel) || $oldbonuslevel > $newbonuslevel){
                       $data['bonuslevel'] = $info['bonuslevel'];
                    } 
                }

                //删除其他手机号相同用户信息
                pdo_delete('sz_yi_member', array('openid' => $oldopenid));
                $newid = $member['id'];
                $oldid = $info['id'];
                //当前用户是否大于其他用户
                if($member['createtime'] > $info['createtime']){
                    if($info['agentid'] != 0){
                        //大于则使用老的用户id
                        $newid = $info['id'];
                        $oldid = $member['id'];
                        pdo_update('sz_yi_member', array('id' => $newid), array('openid' => $openid, 'uniacid' => $_W['uniacid']));
                        //修改新用户，所有用户agentid为老的用户id
                        pdo_update('sz_yi_member', array('agentid' => $newid), array('agentid' => $member['id'], 'uniacid' => $_W['uniacid']));
                    }
                }else{
                    //修改老用户的agentid改为新用户id
                    pdo_update('sz_yi_member', array('agentid' => $newid), array('agentid' => $info['id'], 'uniacid' => $_W['uniacid']));
                }

                //修改全反表
                if(p("return")){
                    pdo_update('sz_yi_return', array('mid' => $newid), array('mid' => $oldid, 'uniacid' => $_W['uniacid']));
                    pdo_update('sz_yi_return_log', array('mid' => $newid, 'openid' => $openid), array('mid' => $oldid, 'uniacid' => $_W['uniacid']));
                    pdo_update('sz_yi_return_money', array('mid' => $newid), array('mid' => $oldid, 'uniacid' => $_W['uniacid']));
                    pdo_update('sz_yi_return_tpm', array('mid' => $newid), array('mid' => $oldid, 'uniacid' => $_W['uniacid']));
                }

                //修改分红表
                if(p("bonus")){
                    pdo_update('sz_yi_bonus_apply', array('mid' => $newid), array('mid' => $oldid, 'uniacid' => $_W['uniacid']));
                    pdo_update('sz_yi_bonus_goods', array('mid' => $newid), array('mid' => $oldid, 'uniacid' => $_W['uniacid']));
                    pdo_update('sz_yi_bonus_log', array('openid' => $openid), array('openid' => $oldopenid, 'uniacid' => $_W['uniacid']));
                }

                //是否绑定app
                if ($info['bindapp'] == 1 || $member['bindapp'] == 1) {
                    $data['bindapp'] = 1;
                }

                //分销状态
                if ($info['status'] == 1 || $member['status'] == 1) {
                    $data['status'] = 1;
                }

                //是否分销商
                if ($info['isagent'] == 1 || $member['isagent'] == 1) {
                    $data['isagent'] = 1;
                }
               
                pdo_update('sz_yi_member', $data, array('openid' => $openid, 'uniacid' => $_W['uniacid']));

                $mc_member = pdo_fetch('select * from ' . tablename('mc_mapping_fans') . ' where openid=:openid and uniacid=:uniacid', array(':uniacid' => $_W['uniacid'], ':openid' => $oldopenid));

                if (!empty($mc_member)) {
                    pdo_delete('mc_mapping_fans', array('openid' => $oldopenid, 'uniacid' => $_W['uniacid']));
                    if (!empty($mc_member['uid'])) {
                        pdo_delete('mc_members', array('uid' => $mc_member['uid'], 'uniacid' => $_W['uniacid']));
                    }
                }
            }
            $path = IA_ROOT . '/addons/sz_yi/data/bindmember/' . $_W['uniacid'];
            if (!is_dir($path)) {
                load()->func('file');
                @mkdirs($path, '0777');
            }
            $log .= "绑定新号++++++++++++++++++++++++++++++++";
            $member = m('member')->getMember($openid);
            $log .= print_r($member, 1);
            $log .= "绑定结束++++++++++++++++++++++++++++++++";
            @file_put_contents($path.'/'.date('Ymd').".log", $log, FILE_APPEND);
            show_json(1, array(
                'preurl' => $preUrl
            ));
        } else {
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
                )
            );

            show_json(1, array(
                'preurl' => $preUrl
            ));
        }
    }
}
include $this->template('member/bindmobile');
