<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 16/11/1
 * Time: 上午11:30
 */

global $_GPC, $_W;

$openid = $_GPC['openid'];
$member = m('member')->getMember($openid);

$foo = ($_GPC['foo'] == 'bind' || $_GPC['foo'] == '') ? 'bind' : 'unbind';
$setting = uni_setting($_W['uniacid'], array('uc'));
if($foo == 'bind') {
    if($setting['uc']['status'] == '1') {
        $uc = $setting['uc'];
        $sql = 'SELECT * FROM ' . tablename('mc_mapping_ucenter') . ' WHERE `uniacid`=:uniacid AND `uid`=:uid';
        $pars = array();
        $pars[':uniacid'] = $_W['uniacid'];
        $pars[':uid'] = $member['uid'];
        $mapping = pdo_fetch($sql, $pars);
        if(empty($mapping)) {
            $op = trim($_GPC['op']);
            if($op == 'yes') {
                if(checksubmit('submit')) {
                    $username = trim($_GPC['username']) ? trim($_GPC['username']) : @message('请填写用户名！', '', 'error');
                    $password = trim($_GPC['password']) ? trim($_GPC['password']) : @message('请填写密码！', '', 'error');
                    mc_init_uc();
                    $data = uc_user_login($username, $password);
                    if($data[0] < 0) {
                        if($data[0] == -1) @message('用户不存在，或者被删除！', '', 'error');
                        elseif ($data[0] == -2) @message('密码错误！', '', 'error');
                        elseif ($data[0] == -3) @message('安全提问错误！', '', 'error');
                    }

                    $exist = pdo_fetch('SELECT * FROM ' . tablename('mc_mapping_ucenter') . ' WHERE `uniacid`=:uniacid AND `centeruid`=:centeruid', array(':uniacid' => $_W['uniacid'], 'centeruid' => $data[0]));
                    if(empty($exist)) {
                        pdo_insert('mc_mapping_ucenter', array('uniacid' => $_W['uniacid'], 'uid' => $member['uid'], 'centeruid' => $data[0]));

                        //同步会员现有积分
                        $credits     = pdo_fetchcolumn("SELECT `credit1` FROM " . tablename('mc_members') . " WHERE `uid` = :uid", array(
                            ':uid' => $member['uid']
                        ));

                        $this->model->setCredits($openid, floatval($credits), 1);

                        @message('绑定UC账号成功！', $this->createMobileUrl('member'), 'success');
                    } else {
                        @message('该UC账号已绑定过,请使用其他账号绑定！', '', 'error');
                    }
                }
            } elseif($op == 'no') {
                if(checksubmit('submit')) {
                    $username = trim($_GPC['username']) ? trim($_GPC['username']) : @message('请填写用户名！', '', 'error');
                    $password = trim($_GPC['password']) ? trim($_GPC['password']) : @message('请填写密码！', '', 'error');
                    $repassword = trim($_GPC['repassword']) ? trim($_GPC['repassword']) : @message('请填写确认密码！', '', 'error');
                    if($password != $repassword) {@message('两次密码输入不一致！', '', 'error');}
                    $email = trim($_GPC['email']) ? trim($_GPC['email']) : @message('请填写邮箱！', '', 'error');
                    mc_init_uc();
                    $uid = uc_user_register($username, $password, $email);

                    $this->model->RegisterDzMember($uid, $username, $email, $password);

                    if($uid < 0) {
                        if($uid == -1) @message('用户名不合法！', '', 'error');
                        elseif ($uid == -2) @message('包含不允许注册的词语！', '', 'error');
                        elseif ($uid == -3) @message('用户名已经存在！', '', 'error');
                        elseif ($uid == -4) @message('邮箱格式错误！', '', 'error');
                        elseif ($uid == -5) @message('邮箱不允许注册！', '', 'error');
                        elseif ($uid == -6) @message('邮箱已经被注册！', '', 'error');
                    } else {
                        if($_W['member']['email'] == '') {
                            mc_update($member['uid'],array('email' => $email));
                        }
                        pdo_insert('mc_mapping_ucenter', array('uniacid' => $_W['uniacid'], 'uid' => $member['uid'], 'centeruid' => $uid));

                        //同步会员现有积分
                        $credits     = pdo_fetchcolumn("SELECT `credit1` FROM " . tablename('mc_members') . " WHERE `uid` = :uid", array(
                            ':uid' => $member['uid']
                        ));

                        $this->model->setCredits($openid, floatval($credits), 1);

                        @message('绑定UC账号成功！', $this->createMobileUrl('member'), 'success');
                    }
                }
            }
            include $this->template('bind');
            exit;
        } else {
            @message('已绑定UC账号,您可以尝试解绑定后重新绑定UC账号！', '', 'error');
        }
    } else {
        @message('系统尚未开启UC！', '', 'success');
    }
} else {
    $result = pdo_delete('mc_mapping_ucenter', array('uid' => $member['uid'], 'uniacid' => $_W['uniacid']), ' AND ');
    if($result === false) {
        @message('解除绑定UC账号失败！', referer(), 'error');
    } else {
        @message('解除绑定UC账号成功！', referer(), 'success');
    }
}
exit('Error: -1');