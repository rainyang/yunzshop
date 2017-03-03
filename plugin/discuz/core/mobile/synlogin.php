<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/2/15
 * Time: 下午5:53
 */
global $_GPC, $_W;

$operation   = !empty($_GPC['op']) ? $_GPC['op'] : 'display' ;
mc_init_uc();

if ($operation == 'display') {
    //Discuz数据库连接
    $setting = uni_setting($_W['uniacid'], array('uc'));

    if($setting['uc']['status'] == '1') {

        $exist = $this->model->hasId();
        if (empty($exist)) {
            //$this->userRegister();
            header('location: ' . $this->createPluginMobileUrl('discuz/synlogin', array('op' => 'uc_register')));
            exit;
        }

        $this->model->userLogin();
    } else {
        @message('系统尚未开启UC！', '', 'success');
    }
} elseif ($operation == 'uc_register') {
    if ($_W['ispost']) {
        $username = $_GPC['username'];
        $password = $_GPC['password'];
        $email = $_GPC['email'];

        if (empty($_W['member'])) {
            @message('未关注公众号！', '', 'error');
        }

        $uid = uc_user_register($username, $password, $email);

        if($uid < 0) {
            if($uid == -1) @message('用户名不合法！', '', 'error');
            elseif ($uid == -2) @message('包含不允许注册的词语！', '', 'error');
            elseif ($uid == -3) @message('用户名已经存在！', '', 'error');
            elseif ($uid == -4) @message('邮箱格式错误！', '', 'error');
            elseif ($uid == -5) @message('邮箱不允许注册！', '', 'error');
            elseif ($uid == -6) @message('邮箱已经被注册！', '', 'error');
        } else {
            $this->model->RegisterDzMember($uid, $username, $email, $password);

            if($_W['member']['email'] == '') {
                mc_update($_W['member']['uid'],array('email' => $email));
            }
            pdo_insert('mc_mapping_ucenter', array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'centeruid' => $uid));

            $nickName = $this->model->getMemberNickName();

            $dzdata = array(
                'field1'=> $nickName,
            );

            $this->model->updateUserInfo($_W['member']['uid'], $dzdata, 1);

            $this->model->userLogin();
        }
    }
}

load()->func('tpl');
include $this->template('uc_register');

