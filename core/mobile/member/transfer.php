<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
load()->func('file');
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$openid    = m('user')->getOpenid();
$member = m('member')->getMember($openid);
$uniacid   = $_W['uniacid'];
$trade     = m('common')->getSysset('trade');
if($operation == 'assigns'){
    if ($_W['isajax']) {
        $assigns_id = intval($_GPC['assigns_id']);
        $assigns = pdo_fetch("select * from " . tablename('sz_yi_member') . " where uniacid=:uniacid and id = :assigns_id ", array(
            ':uniacid'=> $uniacid,
            ':assigns_id'=>$assigns_id
        ));
        if($assigns){
            if($assigns['id'] == $member['id']){
                show_json(0,"受让人不可以是您自己！");
                exit;
            }
            show_json(1, $assigns);
        }else{
            show_json(-1);
        }
    }
}elseif($operation == 'submit'){
    if ($_W['isajax']) {
        //创建文件锁
        $tmpdir = IA_ROOT . "/addons/sz_yi/tmp/member";
        $file   = $tmpdir."/".$member['openid'].".txt";
        if (!is_dir($tmpdir)) {
            mkdirs($tmpdir);
        }
        if (!file_exists($file)) {
            touch($file);
            $money = floatval($_GPC['money']);
            if ($money <= 0 || $member['credit2'] < $money){
                show_json(0,'转让金额不正确');
            }
            $assigns_id = intval($_GPC['assigns']);
            $assigns = pdo_fetch("select * from " . tablename('sz_yi_member') . " where uniacid=:uniacid and id = :assigns_id ", array(
                ':uniacid'=> $uniacid,
                ':assigns_id'=>$assigns_id
            ));
            if ($assigns) {
                $mc_assigns = m('member')->getMember($assigns['openid']);
                m('member')->setCredit($member['openid'],'credit2',-$money);
                $messages = array(
                    'keyword1' => array(
                        'value' => '转增通知', 
                        'color' => '#73a68d'
                    ),
                    'keyword2' => array(
                        'value' => '你向'.$assigns['nickname'].'转增金额'.$money."元！",
                        'color' => '#73a68d'
                    )
                );
                m('message')->sendCustomNotice($member['openid'], $messages);
                m('member')->setCredit($assigns['openid'],'credit2',$money, array(0, '会员余额转让所得：' . $money . " 元"));
                $messages = array(
                    'keyword1' => array(
                        'value' => '转增通知', 
                        'color' => '#73a68d'
                    ),
                    'keyword2' => array(
                        'value' => $member['nickname'].'向你转增金额'.$money."元！",
                        'color' => '#73a68d'
                    )
                );
                m('message')->sendCustomNotice($assigns['openid'], $messages);
                $member_data = array(
                    'uniacid'       => $_W['uniacid'],
                    'openid'        => $openid,
                    'tosell_id'     => $member['id'],
                    'assigns_id'     => $assigns_id,
                    'createtime'    => time(),
                    'status'        => 1,
                    'money'         => $money,
                    'tosell_current_credit'     => $member['credit2'] - $money,
                    'assigns_current_credit'    => $assigns['credit2'] + $money,
                );
                pdo_insert('sz_yi_member_transfer_log', $member_data); 
                @unlink ($file); 
                show_json(1);
            } else {
                @unlink ($file); 
                show_json(0,'受让人不存在！');
            }
            
        }
    }
}
include $this->template('member/transfer');
